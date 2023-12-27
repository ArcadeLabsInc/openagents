import { SidebarLayout } from "@/Layouts/SidebarLayout"
import { usePage } from "@inertiajs/react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { LGraph, LGraphCanvas, LiteGraph } from 'litegraph.js'
import 'litegraph.js/css/litegraph.css'
import { useEffect } from 'react'

// We show all relevant agent nodes, starting with steps.

interface Agent {
  tasks: Task[]
}

interface Task {
  description: string
  steps: Step[]
}

interface Step {
  agent_id: number
  category: string
  created_at: string
  description: string
  entry_type: string
  error_message: string
  id: number
  name: string
  order: number
  params: any
  success_action: string
  task_id: number
  updated_at: string
}

function AgentNodes() {
  const props = usePage().props as any
  const agent = props.agent as Agent
  const task = agent.tasks[0] as Task
  const steps = task.steps as Step[]

  useEffect(() => {
    var graph = new LGraph();

    var canvas = new LGraphCanvas("#mycanvas", graph, { autoresize: true });
    canvas.resize()

    steps.forEach((step, index) => {
      // Using a general-purpose node type and setting the title
      var node = LiteGraph.createNode("basic/data");
      node.title = step.name;
      node.pos = [100, 100 + index * 100];
      graph.add(node);

      // Customize the node as needed
      node.setValue(step.description);
    });

    graph.start();
  }, [])

  return (
    <canvas id="mycanvas" className="w-screen h-screen" />
  )
}

AgentNodes.layout = (page) => <SidebarLayout children={page} grid={true} />

export default AgentNodes
