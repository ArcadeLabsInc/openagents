import InspectLayout from '@/Layouts/InspectLayout';
import { Link, usePage } from '@inertiajs/react';
import { RunDetails, Task } from '@/Components/RunDetails';
import { Run as RunType } from '@/Components/RunTable';
import { Button } from '@/Components/ui/button';
import { useEffect } from 'react';

const Run = () => {
  const { props } = usePage();
  const run = props.run as RunType;
  const steps = props.steps
  const task = props.task as Task
  // console.log({ run, steps, task })

  useEffect(() => {
    // @ts-ignore
    window.Echo.private(`run.${run.id}`)
      .listen('StepCreated', (e) => {
        console.log(e);
      });
  }, [])

  // Assuming `run` contains the stats and task data, otherwise, you would fetch or calculate them here.
  const runStats = {
    revenue: 123, // run.revenue,
    apiCalls: 12, // run.apiCalls,
    usage: 13, // run.usage,
    status: "completed" // run.status,
  };

  // const task = {
  //     description: run.task.description,
  //     steps: run.task.steps,
  // };

  return (
    <div className="pt-8 mx-auto px-4 w-full lg:w-3/4">
      {/* go back */}
      <Link href="/dashboard" className="px-8">
        <Button variant="outline">
          &larr; Back to dashboard
        </Button>
      </Link>
      <RunDetails runStats={runStats} steps={steps} task={task} />
    </div>
  );
};

Run.layout = (page) => <InspectLayout children={page} title="Run" />;

export default Run;
