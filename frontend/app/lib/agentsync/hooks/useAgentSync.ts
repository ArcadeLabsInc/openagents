import { useEffect } from "react";
import { v4 as uuid } from "uuid";
import { useMessagesStore } from "~/stores/messages";

const INITIAL_STATE = {
  isOnline: true,
  lastSyncId: 0,
  pendingChanges: 0,
};

interface AgentSyncOptions {
  scope: string;
  conversationId?: string;
}

export function useAgentSync({ scope, conversationId }: AgentSyncOptions) {
  const { addMessage } = useMessagesStore();

  const handleOnline = () => {
    // TODO: Implement online handler
  };

  const handleOffline = () => {
    // TODO: Implement offline handler
  };

  useEffect(() => {
    window.addEventListener("online", handleOnline);
    window.addEventListener("offline", handleOffline);

    return () => {
      window.removeEventListener("online", handleOnline);
      window.removeEventListener("offline", handleOffline);
    };
  }, []);

  const sendMessage = async (message: string, repos?: string[]) => {
    // If we have a conversation ID, this is a follow-up message
    if (conversationId) {
      const response = await fetch("/api/send-message", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          conversation_id: conversationId,
          message,
          repos,
        }),
      });

      if (!response.ok) {
        throw new Error("Failed to send message");
      }

      const data = await response.json();

      // Store the message
      addMessage(conversationId, {
        id: data.id,
        role: "user",
        content: data.message,
        metadata: repos ? { repos } : undefined,
      });

      return data;
    }

    // Otherwise, this is a new conversation
    const chatId = uuid();
    const response = await fetch("/api/start-repo-chat", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id: chatId,
        message,
        repos: repos || [],
        scope,
      }),
    });

    if (!response.ok) {
      throw new Error("Failed to send message");
    }

    const data = await response.json();

    // Store first message
    addMessage(data.id, {
      id: chatId,
      role: "user",
      content: data.initial_message,
      metadata: repos ? { repos } : undefined,
    });

    return data;
  };

  return {
    state: INITIAL_STATE,
    sendMessage,
  };
}