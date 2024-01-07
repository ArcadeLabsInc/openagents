import { Button } from "@/Components/ui/button";

export const SimpleBuilder = () => {
  return (
    <div className="flex items-center justify-center pt-8">
      <div className="max-w-3xl w-full p-4">
        <div className="mb-6">
          <div className="mb-8 text-xl font-medium md:text-2xl">Create an Agent</div>
          <div className="mb-1.5 flex items-center">
            <span className="" data-state="closed">
              <label className="block font-medium text-token-text-primary">Name</label>
            </span>
          </div>
          <input
            type="text"
            placeholder="Name your Agent"
            className="w-full rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-400 border border-token-border-medium h-9 dark:bg-gray-800"
            value=""
          />
        </div>
        <div className="mb-6 mt-4">
          <div className="mb-1.5 flex items-center">
            <span className="" data-state="closed">
              <label className="block font-medium text-token-text-primary">Description</label>
            </span>
          </div>
          <input
            type="text"
            placeholder="Add a short description about what this Agent does"
            className="w-full rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-400 border border-token-border-medium h-9 dark:bg-gray-800"
            value=""
          />
        </div>
        <div className="mb-6">
          <div className="mb-1.5 flex items-center">
            <span className="" data-state="closed">
              <label className="block font-medium text-token-text-primary">Instructions</label>
            </span>
          </div>
          <div className="relative">
            <textarea
              className="w-full text-sm rounded-lg border px-3 py-2 focus:ring-2 focus:ring-blue-400 border-token-border-medium dark:bg-gray-800 bg-white h-32 resize-none"
              rows={8}
              placeholder="What does this Agent do? How does it behave? What should it avoid doing?"
            ></textarea>
          </div>
        </div>
        <div className="mb-6">
          <div className="mb-1.5 flex items-center">
            <span className="" data-state="closed">
              <label className="block font-medium text-token-text-primary">Welcome Message</label>
            </span>
          </div>
          <div className="relative">
            <textarea
              className="w-full text-sm rounded-lg border px-3 py-2 focus:ring-2 focus:ring-blue-400 border-token-border-medium dark:bg-gray-800 bg-white h-16 resize-none"
              placeholder="How this Agent starts conversations."
            ></textarea>
          </div>
        </div>
        <div className="mb-6">
          <div className="mb-1.5 flex items-center">
            <span className="" data-state="closed">
              <label className="block font-medium text-token-text-primary">Knowledge</label>
            </span>
          </div>
          <div className="flex flex-col gap-4">
            <div className="rounded-lg text-gray-500">
              If you upload files under Knowledge, conversations with your Agent may include file contents.
            </div>
            <div>
              <button className="btn relative btn-neutral h-8 rounded-lg border-token-border-light font-medium">
                <div className="flex w-full gap-2 items-center justify-center">
                  <input multiple={false} type="file" tabIndex={-1} style={{ display: 'none' }} />
                  Upload files
                </div>
              </button>
            </div>
          </div>
          <Button disabled className="mt-12">Create</Button>
        </div>
      </div>
    </div>
  );
};
