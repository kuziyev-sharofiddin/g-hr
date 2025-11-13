import { useForm } from '@inertiajs/react';
import { FormEventHandler, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/input-error';

interface DismissedWorkerReason {
    id: number;
    name: string;
    responsible_worker: string;
}

interface DismissedWorkerReasonFormDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    reason?: DismissedWorkerReason | null;
}

export default function DismissedWorkerReasonFormDialog({
    open,
    onOpenChange,
    reason,
}: DismissedWorkerReasonFormDialogProps) {
    const { data, setData, post, put, processing, errors, reset } = useForm<{
        name: string;
    }>({
        name: '',
    });

    useEffect(() => {
        if (reason) {
            setData({
                name: reason.name,
            });
        } else {
            // Reset form for new reason
            reset();
        }
    }, [reason, open]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (reason) {
            put(`/documents/dismissed-worker-reasons/${reason.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    onOpenChange(false);
                    reset();
                },
            });
        } else {
            post('/documents/dismissed-worker-reasons', {
                preserveScroll: true,
                onSuccess: () => {
                    onOpenChange(false);
                    reset();
                },
            });
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[500px]">
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle>
                            {reason ? 'Sababi tahrirlash' : 'Yangi sabab qo\'shish'}
                        </DialogTitle>
                        <DialogDescription>
                            {reason
                                ? 'Sabab ma\'lumotlarini o\'zgartiring va saqlang'
                                : 'Yangi sabab qo\'shish uchun quyidagi ma\'lumotlarni kiriting'}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="name">
                                Ishdan chiqarish sababi <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Masalan: Maznuniati"
                            />
                            <InputError message={errors.name} />
                        </div>

                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => onOpenChange(false)}
                            disabled={processing}
                        >
                            Bekor qilish
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Saqlanmoqda...' : reason ? 'Saqlash' : 'Qo\'shish'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
