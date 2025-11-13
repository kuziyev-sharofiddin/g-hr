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

interface Section {
    id: number;
    name: string;
    responsible_worker: string;
}

interface SectionFormDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    section?: Section | null;
}

export default function SectionFormDialog({
    open,
    onOpenChange,
    section,
}: SectionFormDialogProps) {
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
    });

    useEffect(() => {
        if (section) {
            setData({
                name: section.name,
            });
        } else {
            reset();
        }
    }, [section, open]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (section) {
            put(`/documents/sections/${section.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    onOpenChange(false);
                    reset();
                },
            });
        } else {
            post('/documents/sections', {
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
                            {section ? 'Bo\'limni tahrirlash' : 'Yangi bo\'lim qo\'shish'}
                        </DialogTitle>
                        <DialogDescription>
                            {section
                                ? 'Bo\'lim ma\'lumotlarini o\'zgartiring va saqlang'
                                : 'Yangi bo\'lim qo\'shish uchun quyidagi ma\'lumotlarni kiriting'}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="name">
                                Bo'lim nomi <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Masalan: Marketing bo'limi"
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
                            {processing ? 'Saqlanmoqda...' : section ? 'Saqlash' : 'Qo\'shish'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
