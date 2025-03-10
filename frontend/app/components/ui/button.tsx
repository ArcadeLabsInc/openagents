import { cva } from "class-variance-authority";
import * as React from "react";
import { cn } from "~/lib/utils";
import { Slot } from "@radix-ui/react-slot";

import type { VariantProps } from "class-variance-authority";
const buttonVariants = cva(
  "inline-flex items-center justify-center gap-3 whitespace-nowrap text-sm font-medium transition-[color,box-shadow] disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 [&_svg]:shrink-0 ring-ring/10 dark:ring-ring/20 dark:outline-ring/40 outline-ring/50 focus-visible:ring-4 focus-visible:outline-1 aria-invalid:focus-visible:ring-0 cursor-pointer relative",
  {
    variants: {
      variant: {
        default:
          "bg-primary text-primary-foreground shadow-sm hover:bg-primary/90",
        destructive:
          "bg-destructive text-destructive-foreground shadow-xs hover:bg-destructive/90",
        outline:
          "border border-input bg-background shadow-xs hover:bg-accent hover:text-accent-foreground",
        secondary:
          "bg-secondary text-secondary-foreground shadow-xs hover:bg-secondary/80",
        ghost: "hover:bg-accent hover:text-accent-foreground",
        link: "text-primary underline-offset-4 hover:underline",
        nav: "bg-black hover:bg-zinc-900 text-white border border-white shadow-nav hover:shadow-nav-hover active:shadow-nav-active transition-all duration-nav ease-nav select-none",
      },
      size: {
        default: "h-9 px-4 py-2 has-[>svg]:px-3",
        sm: "h-8 px-1 has-[>svg]:px-2.5 text-sm",
        lg: "h-12 px-8 py-3 text-lg [&_svg]:size-5",
        icon: "size-9",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  },
);

function Button({
  className,
  variant,
  size,
  asChild = false,
  showDot = false,
  ...props
}: React.ComponentProps<"button"> &
  VariantProps<typeof buttonVariants> & {
    asChild?: boolean;
    showDot?: boolean;
  }) {
  const Comp = asChild ? Slot : "button";

  return (
    <Comp
      data-slot="button"
      className={cn(buttonVariants({ variant, size, className }))}
      {...props}
    >
      {props.children}
      {showDot && <span className="w-1.5 h-1.5 rounded-full bg-current" />}
    </Comp>
  );
}

export { Button, buttonVariants };
