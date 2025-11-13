import { useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useEffect, useState } from 'react';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import InputError from '@/components/input-error';

interface Position {
    id: number;
    name: string;
    responsible_worker: string;
    section_id: number | null;
    does_it_belong_to_the_curator: boolean;
}

interface PositionFormDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    position?: Position | null;
}

export default function PositionFormDialog({
    open,
    onOpenChange,
    position,
}: PositionFormDialogProps) {
    const [sections, setSections] = useState<Array<{ id: number; name: string }>>([]);

    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
        section_id: null as number | null,
    });

    // Fetch sections when dialog opens
    useEffect(() => {
        if (open) {
            const fetchSections = async () => {
                try {
                    const response = await fetch('/documents/sections/list');
                    const responseData = await response.json();
                    if (responseData.data && Array.isArray(responseData.data)) {
                        setSections(responseData.data);
                    }
                } catch (error) {
                    console.error('Failed to fetch sections:', error);
                }
            };
            fetchSections();
        }
    }, [open]);

    useEffect(() => {
        if (position) {
            setData({
                name: position.name,
                section_id: position.section_id || null,
            });
        } else {
            // Reset form for new position
            reset();
        }
    }, [position, open]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (position) {
            put(`/documents/positions/${position.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    onOpenChange(false);
                    reset();
                },
                onError: (errors) => {
                    console.error('Update error:', errors);
                },
            });
        } else {
            post('/documents/positions', {
                preserveScroll: true,
                onSuccess: () => {
                    onOpenChange(false);
                    reset();
                },
                onError: (errors) => {
                    console.error('Create error:', errors);
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
                            {position ? 'Lavozimni tahrirlash' : 'Yangi lavozim qo\'shish'}
                        </DialogTitle>
                        <DialogDescription>
                            {position
                                ? 'Lavozim ma\'lumotlarini o\'zgartiring va saqlang'
                                : 'Yangi lavozim qo\'shish uchun quyidagi ma\'lumotlarni kiriting'}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="name">
                                Lavozim nomi <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Masalan: Bosh direktor"
                            />
                            <InputError message={errors.name} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="section_id">
                                Bo'lim <span className="text-destructive">*</span>
                            </Label>
                            <Select
                                value={data.section_id?.toString() || ''}
                                onValueChange={(value) => setData('section_id', parseInt(value))}
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Bo'lim tanlang" />
                                </SelectTrigger>
                                <SelectContent>
                                    {sections.map((section) => (
                                        <SelectItem key={section.id} value={section.id.toString()}>
                                            {section.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={errors.section_id} />
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
                            {processing ? 'Saqlanmoqda...' : position ? 'Saqlash' : 'Qo\'shish'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
