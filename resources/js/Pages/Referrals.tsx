import { usePage } from "@inertiajs/react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "../Components/ui/card"
import { NavLayout } from "@/Layouts/NavLayout"

export default function Referrals() {
  const props = usePage().props as any
  const referrals = props.referrals
  const user = props.auth.user
  const username = user?.twitter_nickname || user?.github_nickname || 'Guest'
  return (
    <NavLayout user={props.auth.user}>
      <Card className="mt-24 mx-auto w-[350px]">
        <CardHeader>
          <CardTitle>Your referral link</CardTitle>
          <CardDescription>Earn agent credit when your referrals spend money</CardDescription>
        </CardHeader>

        <CardContent>
          <p>https://openagents.com/?r={username}</p>
        </CardContent>
        {/* <CardFooter>
        <Button>Share</Button>
      </CardFooter> */}
      </Card>

      <Card className="mt-12 mx-auto w-[350px]">
        <CardHeader>
          <CardTitle>Referrals ({referrals.length})</CardTitle>
          <CardDescription>List of users you referred</CardDescription>
        </CardHeader>

        <table>
          <tbody>
            {referrals.map((referral: any) => {
              return <tr key={referral.id}>
                <td>{referral.github_nickname}</td>
              </tr>
            })}
          </tbody>
        </table>
      </Card>
    </NavLayout>
  )
}
