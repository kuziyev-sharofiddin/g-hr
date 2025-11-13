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

interface ApplicationTitle {
    id: number;
    name: string;
    responsible_worker: string;
}

interface ApplicationTitleFormDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    title?: ApplicationTitle | null;
}

export default function ApplicationTitleFormDialog({
    open,
    onOpenChange,
    title,
}: ApplicationTitleFormDialogProps) {
    const { data, setData, post, put, processing, errors, reset } = useForm<{
        name: string;
    }>({
        name: '',
    });

    useEffect(() => {
        if (title) {
            setData({
                name: title.name,
            });
        } else {
            reset();
        }
    }, [title, open]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (title) {
            put(`/documents/application-titles/${title.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    onOpenChange(false);
                    reset();
                },
            });
        } else {
            post('/documents/application-titles', {
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
                            {title ? 'Mavzuni tahrirlash' : 'Yangi mavzu qo\'shish'}
                        </DialogTitle>
                        <DialogDescription>
                            {title
                                ? 'Mavzu ma\'lumotlarini o\'zgartiring va saqlang'
                                : 'Yangi mavzu qo\'shish uchun quyidagi ma\'lumotlarni kiriting'}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="name">
                                Mavzu nomi <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Masalan: Oqilgan ta'minot"
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
                            {processing ? 'Saqlanmoqda...' : title ? 'Saqlash' : 'Qo\'shish'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
