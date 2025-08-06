import React from 'react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Head, router, useForm } from '@inertiajs/react';



export default function CreateLeaveRequest() {
    const { data, setData, post, processing, errors } = useForm({
        from_date: '',
        to_date: '',
        type: 'personal',
        reason: '',
        evidence_file: null as File | null,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        post(route('leave-requests.store'), {
            onSuccess: () => {
                router.visit(route('leave-requests.index'));
            },
        });
    };

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            setData('evidence_file', e.target.files[0]);
        }
    };

    return (
        <AppShell>
            <Head title="Request Leave" />
            
            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-gray-100">
                        📝 Request Leave
                    </h1>
                    <p className="text-gray-600 dark:text-gray-400">
                        Submit a leave request for review by your teacher or admin
                    </p>
                </div>

                {/* Form */}
                <form onSubmit={handleSubmit} className="max-w-2xl">
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700 space-y-6">
                        {/* Date Range */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label htmlFor="from_date" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    From Date *
                                </label>
                                <input
                                    type="date"
                                    id="from_date"
                                    value={data.from_date}
                                    onChange={(e) => setData('from_date', e.target.value)}
                                    min={new Date().toISOString().split('T')[0]}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                    required
                                />
                                {errors.from_date && (
                                    <p className="text-red-600 text-sm mt-1">{errors.from_date}</p>
                                )}
                            </div>
                            
                            <div>
                                <label htmlFor="to_date" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    To Date *
                                </label>
                                <input
                                    type="date"
                                    id="to_date"
                                    value={data.to_date}
                                    onChange={(e) => setData('to_date', e.target.value)}
                                    min={data.from_date || new Date().toISOString().split('T')[0]}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                    required
                                />
                                {errors.to_date && (
                                    <p className="text-red-600 text-sm mt-1">{errors.to_date}</p>
                                )}
                            </div>
                        </div>

                        {/* Leave Type */}
                        <div>
                            <label htmlFor="type" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Leave Type *
                            </label>
                            <select
                                id="type"
                                value={data.type}
                                onChange={(e) => setData('type', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                required
                            >
                                <option value="personal">Personal</option>
                                <option value="sick">Sick</option>
                                <option value="family">Family Emergency</option>
                                <option value="emergency">Emergency</option>
                            </select>
                            {errors.type && (
                                <p className="text-red-600 text-sm mt-1">{errors.type}</p>
                            )}
                        </div>

                        {/* Reason */}
                        <div>
                            <label htmlFor="reason" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Reason *
                            </label>
                            <textarea
                                id="reason"
                                value={data.reason}
                                onChange={(e) => setData('reason', e.target.value)}
                                rows={4}
                                placeholder="Please provide a detailed reason for your leave request..."
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                required
                            />
                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Minimum 10 characters, maximum 1000 characters
                            </p>
                            {errors.reason && (
                                <p className="text-red-600 text-sm mt-1">{errors.reason}</p>
                            )}
                        </div>

                        {/* Evidence File */}
                        <div>
                            <label htmlFor="evidence_file" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Supporting Evidence (Optional)
                            </label>
                            <input
                                type="file"
                                id="evidence_file"
                                accept=".pdf,.jpg,.jpeg,.png"
                                onChange={handleFileChange}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                            />
                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Upload medical certificates, appointment letters, or other supporting documents. 
                                Maximum file size: 5MB. Supported formats: PDF, JPG, JPEG, PNG
                            </p>
                            {errors.evidence_file && (
                                <p className="text-red-600 text-sm mt-1">{errors.evidence_file}</p>
                            )}
                        </div>

                        {/* Guidelines */}
                        <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <h3 className="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">
                                📋 Leave Request Guidelines
                            </h3>
                            <ul className="text-sm text-blue-700 dark:text-blue-400 space-y-1">
                                <li>• Submit requests at least 24 hours in advance when possible</li>
                                <li>• For sick leave, medical certificates may be required for absences over 3 days</li>
                                <li>• Emergency requests will be reviewed as soon as possible</li>
                                <li>• You will be notified via email once your request is reviewed</li>
                            </ul>
                        </div>

                        {/* Form Actions */}
                        <div className="flex justify-between pt-4">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => router.visit(route('leave-requests.index'))}
                            >
                                Cancel
                            </Button>
                            <Button
                                type="submit"
                                disabled={processing}
                                className="bg-indigo-600 hover:bg-indigo-700"
                            >
                                {processing ? 'Submitting...' : 'Submit Request'}
                            </Button>
                        </div>
                    </div>
                </form>
            </div>
        </AppShell>
    );
}