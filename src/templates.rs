
use hypertext::{html_elements, maud, GlobalAttributes, Renderable};


pub mod templates {
    pub fn render_header_template(buttons: Vec<(&str, &str)>) -> String {
        maud! {
            <header class="py-2 mb-5">
                <div class="flex justify-between items-center w-full">
                    <span class="text-lg font-bold">"OpenAgents"</span>
                </div>
                <div class="mt-4">
                    <nav>
                        <ul class="grid grid-cols-3 gap-2 lg:grid-cols-6" id="nav-buttons">
                            @for (href, text) in buttons {
                                <li class="flex justify-center mb-1">
                                    <a class="btn-nav bg-black hover:bg-zinc-900 text-white text-xs inline-flex items-center justify-center whitespace-nowrap select-none text-center align-middle no-underline outline-none w-full px-6 border border-white" href={href}>{text}</a>
                                </li>
                            }
                        </ul>
                    </nav>
                </div>
            </header>
        }
    }

    pub fn render_agent_item_template(agent: &Agent) -> String {
        maud! {
            <div class="agent-item border border-gray-300 rounded-lg p-4 mb-4" data-agent-id={agent.id}>
                <h3 class="text-lg font-bold mb-2">{agent.name}</h3>
                <p class="text-sm text-gray-700 mb-2">{agent.description}</p>
                <div class="agent-status mb-2">
                    "Status: " <span class={format!("status-badge {}", agent.status)}>{agent.status}</span>
                </div>
                <div class="agent-metrics text-sm text-gray-600 mb-2">
                    <div>"Memory: " {agent.memory_usage} "MB / " {agent.memory_limit} "MB"</div>
                    <div>"CPU: " {agent.cpu_usage} "ms / " {agent.cpu_limit} "ms"</div>
                </div>
                <div class="agent-actions">
                    <button nostr-action="start" data-agent-id={agent.id} class="btn-nav bg-black hover:bg-zinc-900 text-white text-xs inline-flex items-center justify-center whitespace-nowrap select-none text-center align-middle no-underline outline-none w-full px-6 border border-white">"Start"</button>
                    <button nostr-action="stop" data-agent-id={agent.id} class="btn-nav bg-black hover:bg-zinc-900 text-white text-xs inline-flex items-center justify-center whitespace-nowrap select-none text-center align-middle no-underline outline-none w-full px-6 border border-white">"Stop"</button>
                    <button nostr-action="delete" data-agent-id={agent.id} class="btn-nav bg-red-600 hover:bg-red-700 text-white text-xs inline-flex items-center justify-center whitespace-nowrap select-none text-center align-middle no-underline outline-none w-full px-6 border border-white">"Delete"</button>
                </div>
            </div>
        }
    }

    pub struct Agent {
        pub id: String,
        pub name: String,
        pub description: String,
        pub status: String,
        pub memory_usage: u32,
        pub memory_limit: u32,
        pub cpu_usage: u32,
        pub cpu_limit: u32,
    }
}
