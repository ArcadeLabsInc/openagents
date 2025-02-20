import { Lightbulb } from "lucide-react"
import {
  Accordion, AccordionContent, AccordionItem, AccordionTrigger
} from "~/components/ui/accordion"
import { cn } from "~/lib/utils"

interface ThinkingProps {
  duration?: number; // duration in seconds
}

export function Thinking({ duration = 0 }: ThinkingProps) {
  return (
    <div className={cn(
      "md:-mx-4 mb-4 relative",
      "border-2 border-toggle-border overflow-clip"
    )}>
      <Accordion type="single" collapsible className="w-full">
        <AccordionItem value="thinking" className="border-none">
          <AccordionTrigger className="group">
            <div className="min-h-[3.5rem] overflow-y-clip flex flex-col justify-start text-primary relative w-full overflow-clip">
              <div className="flex h-full gap-1 w-full items-center justify-start px-5 pt-4">
                <div className="flex items-center gap-2">
                  <div className="flex items-center gap-1 overflow-hidden">
                    <Lightbulb className="text-nowrap shrink-0 h-4 w-4" />
                    <div className="flex items-baseline gap-1 overflow-hidden">
                      <span className="text-sm text-nowrap whitespace-nowrap">Thought for</span>
                      <span className="font-mono text-secondary-foreground font-medium text-sm">{duration}s</span>
                    </div>
                  </div>
                </div>
              </div>
              <span className="text-muted-foreground text-sm my-0 px-5 pb-4 flex flex-row items-center">
                Expand for details
              </span>
            </div>
          </AccordionTrigger>
          <AccordionContent className="text-sm text-muted-foreground px-5 pb-4">
            <div className="space-y-2">
              <p>1. Analyzing the request and breaking it down into steps</p>
              <p>2. Searching relevant documentation and context</p>
              <p>3. Formulating a response based on gathered information</p>
            </div>
          </AccordionContent>
        </AccordionItem>
      </Accordion>
    </div>
  );
}
