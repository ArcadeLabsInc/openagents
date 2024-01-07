import { ChatBar } from "../ChatBar"
import { useState } from "react"
import { MessagesList } from "../MessagesList";

interface ChatPaneProps {
  agentId: number
  initialMessages?: any[]
}

export const ChatPane = ({ agentId, initialMessages }: ChatPaneProps) => {
  const firstMessages = initialMessages ?? { id: 0, role: "assistant", content: "Welcome! I am Concierge, the first OpenAgent.\n\nYou can ask me basic questions about OpenAgents and I will try my best to answer.\n\nClick 'Agent' on the left to see what I know and how I act.\n\nI might lie or say something crazy. Oh well - thank you for testing!", tokens: [] }
  const [messages, setMessages]: any = useState(firstMessages)
  const messagesArray = Object.values(messages) as any[];
  return (
    <div className="relative flex flex-col overflow-hidden sm:overflow-x-visible h-full grow">
      <div className="relative grow overflow-y-hidden">
        <div className="h-full">
          <div className="scrollbar-gutter-both-edges relative h-full overflow-y-auto overflow-x-hidden">
            <div className="t-body-chat relative h-full space-y-6 px-5 text-primary-700 w-full mx-auto max-w-1.5xl 2xl:max-w-[47rem]">
              <div className="relative h-8 shrink-0 2xl:h-12 z-30"></div>
              <div className="pb-6 lg:pb-8 min-h-[calc(100%-60px)] sm:min-h-[calc(100%-120px)]">
                <div className="relative space-y-6">
                  <div className="space-y-6">

                    <div className="break-anywhere relative py-1">
                      <div className="flex items-center">
                        <MessagesList messages={messages} />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div className="max-h-[40%] px-5 sm:px-0 z-15 w-full mx-auto max-w-1.5xl 2xl:max-w-[47rem]">
        <ChatBar agentId={agentId} messages={messagesArray} setMessages={setMessages} />
      </div>
      <div className="px-5 py-2 md:py-5 w-full mx-auto max-w-1.5xl 2xl:max-w-[47rem]"></div>
    </div>
  )
}
