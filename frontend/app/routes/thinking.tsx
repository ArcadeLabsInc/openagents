import { useAgentSync } from "agentsync";
import { ChatInput } from "~/components/chat-input";

export default function ThinkingPage() {
  const { sendMessage, state } = useAgentSync({
    scope: "thinking",
  });

  return (
    <div className="flex flex-col items-center justify-center min-h-screen p-8">
      <div className="max-w-4xl w-full space-y-8">
        <div className="text-center space-y-4">
          <h1 className="text-4xl font-bold tracking-tight">
            OpenAgents Thinking Demo
          </h1>
          <p className="text-lg text-muted-foreground">
            Watch OpenAgents think through complex problems in real-time
          </p>
        </div>

        <div className="bg-muted/50 rounded-lg p-6 space-y-4">
          <h2 className="text-xl font-semibold">How it works</h2>
          <div className="space-y-2">
            <p>1. Enter your task or question below</p>
            <p>2. Add relevant GitHub repositories for context</p>
            <p>3. Watch OpenAgents break down and solve the problem</p>
          </div>
        </div>

        <div className="space-y-4">
          <ChatInput onSubmit={sendMessage} />
          {!state.isOnline && (
            <div className="text-sm text-red-500">
              You are currently offline. Messages will be queued.
            </div>
          )}
          {state.pendingChanges > 0 && (
            <div className="text-sm text-yellow-500">
              {state.pendingChanges} pending changes to sync
            </div>
          )}
        </div>
      </div>
    </div>
  );
}