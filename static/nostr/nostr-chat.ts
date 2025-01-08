import NDK, { NDKEvent, NDKSubscription, NDKNip07Signer } from '@nostr-dev-kit/ndk'
import { NostrChatConfig, ChatState, ChannelMetadata } from './types'
import { ChatStorage } from './storage'

declare global {
  interface Window {
    htmx: any
    nostr?: {
      getPublicKey(): Promise<string>
      signEvent(event: any): Promise<any>
    }
    ndk: NDK
  }
}

class NostrChat {
  private config: NostrChatConfig
  private state: ChatState
  private storage: ChatStorage
  private templates: Map<string, HTMLTemplateElement>
  private signer: NDKNip07Signer | null = null
  private api: any // Store HTMX API reference

  constructor() {
    this.config = {
      defaultRelays: [
        'wss://nostr-pub.wellorder.net',
        'wss://nostr.mom',
        'wss://relay.nostr.band'
      ],
      messageTemplate: '#message-template',
      autoScroll: true,
      moderationEnabled: true,
      pollInterval: 5000,
      messageLimit: 50
    }
    this.storage = new ChatStorage()
    this.templates = new Map()
    this.state = {
      messages: new Map(),
      hiddenMessages: new Set(),
      mutedUsers: new Set(),
      moderationActions: []
    }

    // Bind methods
    this.loadTemplates = this.loadTemplates.bind(this)
    this.setupHtmxExtension = this.setupHtmxExtension.bind(this)
    this.processElement = this.processElement.bind(this)
    this.cleanupElement = this.cleanupElement.bind(this)
    this.handleNewMessage = this.handleNewMessage.bind(this)
    this.handleSubmit = this.handleSubmit.bind(this)
  }

  // Initialize the extension
  async init() {
    console.log('Initializing NostrChat...')
    await this.initializeSigner()
    this.setupHtmxExtension()
    this.loadTemplates()
    this.setupFormHandlers()
    console.log('NostrChat initialized')
  }

  private setupFormHandlers() {
    document.addEventListener('submit', async (e) => {
      const form = e.target as HTMLFormElement
      if (form.getAttribute('nostr-chat-post')) {
        e.preventDefault()
        await this.handleSubmit(form)
      }
    })
  }

  private async handleSubmit(form: HTMLFormElement) {
    console.log('Form submitted')
    const content = new FormData(form).get('content') as string
    if (!content?.trim()) return

    try {
      if (!this.signer) {
        throw new Error('No signer available')
      }

      console.log('Creating message event...')
      const event = await this.createMessageEvent(content)
      console.log('Publishing message:', event)
      await window.ndk.publish(event)
      console.log('Message published successfully')
      form.reset()
      this.dispatchEvent('nostr-chat:message-sent', { event })
    } catch (error) {
      console.error('Failed to send message:', error)
      this.handleError('Failed to send message', error)
      // Show error to user
      const errorDiv = form.querySelector('.error-message') || document.createElement('div')
      errorDiv.className = 'error-message'
      errorDiv.textContent = 'Failed to send message. Make sure your Nostr extension is unlocked.'
      form.appendChild(errorDiv)
    }
  }

  private async initializeSigner() {
    try {
      if (typeof window.nostr !== 'undefined') {
        console.log('Found nostr provider, initializing signer...')
        this.signer = new NDKNip07Signer()
        window.ndk.signer = this.signer
        // Test the signer
        const pubkey = await this.signer.user().then(user => user.pubkey)
        console.log('NIP-07 signer initialized with pubkey:', pubkey)
      } else {
        console.warn('No NIP-07 extension found. Message sending will be disabled.')
        this.disableMessageSending()
      }
    } catch (error) {
      console.error('Failed to initialize NIP-07 signer:', error)
      this.disableMessageSending()
    }
  }

  private disableMessageSending() {
    // Find and disable all message input forms
    document.querySelectorAll('[nostr-chat-post]').forEach(form => {
      const input = form.querySelector('input, textarea')
      const button = form.querySelector('button')
      if (input) input.setAttribute('disabled', 'true')
      if (button) button.setAttribute('disabled', 'true')
      form.setAttribute('title', 'Please install a Nostr extension (like nos2x or Alby) to send messages')
    })
  }

  private setupHtmxExtension() {
    console.log('Setting up HTMX extension...')
    window.htmx.defineExtension('nostr-chat', {
      init: (apiRef: any) => {
        console.log('HTMX extension initialized with API')
        this.api = apiRef
      },
      onEvent: (name: string, evt: CustomEvent) => {
        console.log('HTMX event:', name)
        switch (name) {
          case 'htmx:afterProcessNode':
            this.processElement(evt.target as HTMLElement)
            break
          case 'htmx:beforeCleanupElement':
            this.cleanupElement(evt.target as HTMLElement)
            break
        }
      }
    })
  }

  private loadTemplates() {
    document.querySelectorAll('template[id]').forEach(template => {
      console.log('Loading template:', template.id)
      this.templates.set(template.id, template as HTMLTemplateElement)
    })
  }

  private async processElement(element: HTMLElement) {
    console.log('Processing element:', element)
    
    // Channel subscription
    const channelId = element.getAttribute('nostr-chat-channel')
    if (channelId) {
      console.log('Found channel ID:', channelId)
      await this.subscribeToChannel(channelId, element)
    }

    // Message posting
    if (element.getAttribute('nostr-chat-post')) {
      console.log('Setting up message posting form')
      if (this.signer) {
        this.setupMessagePosting(element as HTMLFormElement)
      } else {
        element.setAttribute('disabled', 'true')
        element.setAttribute('title', 'Please install a Nostr extension to send messages')
      }
    }

    // Moderation controls
    if (this.config.moderationEnabled) {
      const hideId = element.getAttribute('nostr-chat-hide')
      if (hideId) {
        this.setupHideButton(element, hideId)
      }

      const mutePubkey = element.getAttribute('nostr-chat-mute')
      if (mutePubkey) {
        this.setupMuteButton(element, mutePubkey)
      }
    }
  }

  private cleanupElement(element: HTMLElement) {
    const channelId = element.getAttribute('nostr-chat-channel')
    if (channelId && this.state.subscription) {
      console.log('Cleaning up subscription for channel:', channelId)
      this.state.subscription.stop()
      this.state.subscription = undefined
    }
  }

  // Channel Operations
  private async subscribeToChannel(channelId: string, element: HTMLElement) {
    console.log('Subscribing to channel:', channelId)
    this.state.channelId = channelId

    // Load cached metadata
    const cached = this.storage.getChannelMetadata(channelId)
    if (cached) {
      this.renderChannelMetadata(cached, element)
    }

    // Subscribe to channel messages
    const sub = window.ndk.subscribe({
      kinds: [42], // channel messages
      '#e': [channelId],
    }, { closeOnEose: false })

    sub.on('event', (event: NDKEvent) => {
      console.log('Received channel message:', event)
      this.handleNewMessage(event, element)
    })

    this.state.subscription = sub
    sub.start()

    // Also fetch channel metadata
    const metadataSub = window.ndk.subscribe({
      kinds: [41], // channel metadata
      '#e': [channelId],
    })

    metadataSub.on('event', (event: NDKEvent) => {
      console.log('Received channel metadata:', event)
      const metadata = JSON.parse(event.content)
      this.storage.cacheChannelMetadata(channelId, metadata)
      this.renderChannelMetadata(metadata, element)
    })
  }

  // Message Operations
  private setupMessagePosting(form: HTMLFormElement) {
    console.log('Setting up message form:', form)
  }

  private async createMessageEvent(content: string): Promise<NDKEvent> {
    if (!this.state.channelId) throw new Error('No channel selected')

    console.log('Creating message event with content:', content)
    const event = new NDKEvent(window.ndk)
    event.kind = 42
    event.content = content
    event.tags = [['e', this.state.channelId, '', 'root']]
    
    // Ensure the event is properly signed
    await event.sign()
    console.log('Event signed:', event)
    
    return event
  }

  // Moderation
  private setupHideButton(button: HTMLElement, messageId: string) {
    button.addEventListener('click', () => {
      const reason = button.getAttribute('nostr-chat-reason')
      this.storage.hideMessage(messageId, reason)
      this.dispatchEvent('nostr-chat:message-hidden', { messageId, reason })
    })
  }

  private setupMuteButton(button: HTMLElement, pubkey: string) {
    button.addEventListener('click', () => {
      const reason = button.getAttribute('nostr-chat-reason')
      this.storage.muteUser(pubkey, reason)
      this.dispatchEvent('nostr-chat:user-muted', { pubkey, reason })
    })
  }

  private renderMessage(event: NDKEvent): HTMLElement {
    console.log('Rendering message:', event)
    const template = this.templates.get(this.config.messageTemplate?.slice(1) || 'message-template')
    if (!template) throw new Error('Message template not found')

    const clone = template.content.cloneNode(true) as HTMLElement
    // Replace template variables
    const data = {
      id: event.id,
      pubkey: event.pubkey,
      pubkey_short: event.pubkey.slice(0, 8),
      content: event.content,
      created_at: event.created_at,
      formatted_time: new Date(event.created_at * 1000).toLocaleString()
    }

    this.replaceTemplateVariables(clone, data)
    return clone
  }

  private renderChannelMetadata(metadata: ChannelMetadata, element: HTMLElement) {
    console.log('Rendering channel metadata:', metadata)
    const template = this.templates.get('channel-metadata-template')
    if (!template) return

    const clone = template.content.cloneNode(true) as HTMLElement
    this.replaceTemplateVariables(clone, metadata)
    
    const target = element.querySelector('[data-channel-metadata]')
    if (target) {
      target.innerHTML = ''
      target.appendChild(clone)
    }
  }

  private replaceTemplateVariables(element: HTMLElement, data: Record<string, any>) {
    const walker = document.createTreeWalker(
      element,
      NodeFilter.SHOW_TEXT | NodeFilter.SHOW_ELEMENT,
      null
    )

    let node
    while (node = walker.nextNode()) {
      if (node.nodeType === Node.TEXT_NODE) {
        node.textContent = node.textContent?.replace(
          /\{\{(\w+)\}\}/g,
          (_, key) => data[key] || ''
        )
      } else if (node instanceof Element) {
        Array.from(node.attributes).forEach(attr => {
          attr.value = attr.value.replace(
            /\{\{(\w+)\}\}/g,
            (_, key) => data[key] || ''
          )
        })
      }
    }
  }

  // Event Handling
  private async handleNewMessage(event: NDKEvent, container: HTMLElement) {
    console.log('Handling new message:', event)
    if (this.storage.isMessageHidden(event.id) || 
        this.storage.isUserMuted(event.pubkey)) {
      return
    }

    this.state.messages.set(event.id, event)
    const rendered = this.renderMessage(event)
    
    const messagesContainer = container.querySelector('[data-messages]')
    if (messagesContainer) {
      messagesContainer.insertAdjacentElement('beforeend', rendered)
      if (this.config.autoScroll) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight
      }
    }
  }

  private handleError(message: string, error: any) {
    console.error(message, error)
    this.dispatchEvent('nostr-chat:error', { message, error })
  }

  private dispatchEvent(name: string, detail: any) {
    document.dispatchEvent(new CustomEvent(name, { detail }))
  }
}

// Initialize the extension
const nostrChat = new NostrChat()
nostrChat.init()