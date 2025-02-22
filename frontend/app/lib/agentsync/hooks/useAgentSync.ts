import { useEffect, useRef, useState } from "react"
import { v4 as uuid } from "uuid"
import { useMessagesStore } from "~/stores/messages"
import { WebSocketClient } from "./WebSocketClient"

const INITIAL_STATE = {
  isOnline: true,
  lastSyncId: 0,
  pendingChanges: 0,
  isStreaming: false,
  error: null as string | null,
};

interface AgentSyncOptions {
  scope: string;
  conversationId?: string;
  useReasoning?: boolean;
}

interface StreamingState {
  content: string;
  reasoning: string;
}

export function useAgentSync({
  scope,
  conversationId,
  useReasoning = false,
}: AgentSyncOptions) {
  const { addMessage, setMessages, removeMessages } = useMessagesStore();
  const [state, setState] = useState(INITIAL_STATE);
  const wsRef = useRef<WebSocketClient | null>(null);
  const streamingStateRef = useRef<StreamingState>({
    content: "",
    reasoning: "",
  });

  useEffect(() => {
    // Initialize WebSocket with proper URL
    const wsUrl = process.env.NODE_ENV === 'development' 
      ? 'ws://localhost:8000/ws'
      : `wss://${window.location.host}/ws`;

    console.debug("Initializing WebSocket:", wsUrl);
    wsRef.current = new WebSocketClient(wsUrl);

    // Connect and then subscribe
    wsRef.current.connect().then(() => {
      console.debug("Sending subscription:", { scope, conversationId });
      wsRef.current?.send({
        type: "Subscribe",
        scope,
        conversation_id: conversationId,
        last_sync_id: state.lastSyncId,
      });
    }).catch(error => {
      console.error("Failed to connect to WebSocket:", error);
      setState(s => ({ ...s, error: error.message }));
    });

    // Handle incoming messages
    const unsubscribe = wsRef.current.onMessage((msg) => {
      console.debug("Received message:", msg);
      switch (msg.type) {
        case "Subscribed":
          setState((s) => ({ ...s, lastSyncId: msg.last_sync_id, error: null }));
          break;

        case "Update":
          if (msg.delta.content) {
            streamingStateRef.current.content += msg.delta.content;
          }
          if (msg.delta.reasoning) {
            streamingStateRef.current.reasoning += msg.delta.reasoning;
          }

          // Update message in store
          addMessage(conversationId || msg.message_id, {
            id: msg.message_id,
            role: "assistant",
            content: streamingStateRef.current.content,
            reasoning: streamingStateRef.current.reasoning || undefined,
          });
          break;

        case "Complete":
          setState((s) => ({ ...s, isStreaming: false, error: null }));
          break;

        case "Error":
          console.error("WebSocket error:", msg.message);
          setState((s) => ({ 
            ...s, 
            isStreaming: false, 
            error: msg.message 
          }));
          break;
      }
    });

    // Cleanup
    return () => {
      console.debug("Cleaning up WebSocket connection");
      unsubscribe();
      if (wsRef.current) {
        wsRef.current.disconnect();
        wsRef.current = null;
      }
    };
  }, [scope, conversationId]);

  // Handle online/offline status
  useEffect(() => {
    const handleOnline = () => {
      console.debug("Browser online");
      setState((s) => ({ ...s, isOnline: true }));
      if (wsRef.current) {
        wsRef.current.connect().catch(e => {
          console.error("Reconnection failed:", e);
        });
      }
    };

    const handleOffline = () => {
      console.debug("Browser offline");
      setState((s) => ({ ...s, isOnline: false }));
    };

    window.addEventListener("online", handleOnline);
    window.addEventListener("offline", handleOffline);

    return () => {
      window.removeEventListener("online", handleOnline);
      window.removeEventListener("offline", handleOffline);
    };
  }, []);

  const sendMessage = async (message: string, repos?: string[]) => {
    if (!wsRef.current) {
      throw new Error("WebSocket not initialized");
    }

    const messageId = uuid();
    setState((s) => ({ ...s, isStreaming: true, error: null }));
    streamingStateRef.current = { content: "", reasoning: "" };

    try {
      console.debug("Sending message:", { messageId, message, repos });
      
      // Add user message
      addMessage(conversationId || messageId, {
        id: messageId,
        role: "user",
        content: message,
        metadata: repos ? { repos } : undefined,
      });

      // Send message via WebSocket
      wsRef.current.send({
        type: "Message",
        id: messageId,
        conversation_id: conversationId,
        content: message,
        repos,
        use_reasoning: useReasoning,
      });

      return {
        id: messageId,
        message,
      };
    } catch (error) {
      console.error("Error sending message:", error);
      setState((s) => ({ 
        ...s, 
        isStreaming: false,
        error: error instanceof Error ? error.message : "Unknown error"
      }));
      throw error;
    }
  };

  return {
    state,
    sendMessage,
  };
}