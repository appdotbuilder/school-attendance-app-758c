import { AppShell } from '@/components/app-shell';
import { Head } from '@inertiajs/react';



export default function Dashboard() {
    
    // This component should not be reached as the backend controller 
    // routes to specific role dashboards, but just in case
    return (
        <AppShell>
            <Head title="Dashboard" />
            <div className="p-6">
                <h1 className="text-2xl font-bold">Dashboard</h1>
                <p>Loading your dashboard...</p>
            </div>
        </AppShell>
    );
}