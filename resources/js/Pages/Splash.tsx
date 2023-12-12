import { Hero } from "@/Components/landing/Hero";
import { Button } from "@/Components/ui/button";
import SimpleLayout from "@/Layouts/SimpleLayout";
import { GitHubLogoIcon } from "@radix-ui/react-icons";

function Splash() {
    return (
        <Hero />
    )
}

Splash.layout = (page) => <SimpleLayout children={page} title="Inspect" />

export default Splash
