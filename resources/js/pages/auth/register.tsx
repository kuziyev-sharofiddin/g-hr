import { Head, router } from '@inertiajs/react';
import { useEffect } from 'react';

// Register is disabled - only login is allowed
export default function Register() {
    useEffect(() => {
        // Redirect to login page
        router.get('/login');
    }, []);

    return (
        <>
            <Head title="Redirecting..." />
            <div>Redirecting to login...</div>
        </>
    );
}
