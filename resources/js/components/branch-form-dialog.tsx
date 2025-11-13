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

interface Branch {
    id: number;
    name: string;
    address: string;
    phone_number: string;
    target: string;
    location: string;
}

interface BranchFormDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    branch?: Branch | null;
}

export default function BranchFormDialog({
    open,
    onOpenChange,
    branch,
}: BranchFormDialogProps) {
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
        address: '',
        phone_number: '',
        target: '',
        location: '',
    });

    useEffect(() => {
        if (branch) {
            setData({
                name: branch.name,
                address: branch.address || '',
                phone_number: branch.phone_number || '',
                target: branch.target || '',
                location: branch.location || '',
            });
        } else {
            reset();
        }
    }, [branch, open]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (branch) {
            put(`/documents/branches/${branch.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    onOpenChange(false);
                    reset();
                },
            });
        } else {
            post('/documents/branches', {
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
            <DialogContent className="sm:max-w-[500px]" onInteractOutside={(e) => e.preventDefault()}>
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle>
                            {branch ? 'Filialni tahrirlash' : 'Yangi filial qo\'shish'}
                        </DialogTitle>
                        <DialogDescription>
                            {branch
                                ? 'Filial ma\'lumotlarini o\'zgartiring va saqlang'
                                : 'Yangi filial qo\'shish uchun quyidagi ma\'lumotlarni kiriting'}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="name">
                                Filial nomi <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Masalan: Toshkent filiali"
                            />
                            <InputError message={errors.name} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="address">Manzil</Label>
                            <textarea
                                id="address"
                                value={data.address}
                                onChange={(e) => setData('address', e.target.value)}
                                placeholder="Filial manzilini kiriting..."
                                rows={3}
                                className="flex w-full rounded-lg border border-input bg-background px-3 py-2 text-base shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            />
                            <InputError message={errors.address} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="phone_number">Telefon raqam</Label>
                            <Input
                                id="phone_number"
                                type="text"
                                value={data.phone_number}
                                onChange={(e) => setData('phone_number', e.target.value)}
                                placeholder="+998 XX XXX XX XX"
                            />
                            <InputError message={errors.phone_number} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="target">Maqsad</Label>
                            <textarea
                                id="target"
                                value={data.target}
                                onChange={(e) => setData('target', e.target.value)}
                                placeholder="Filial maqsadini kiriting..."
                                rows={2}
                                className="flex w-full rounded-lg border border-input bg-background px-3 py-2 text-base shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            />
                            <InputError message={errors.target} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="location">
                                Joylashuv (latitude, longitude)
                            </Label>
                            <Input
                                id="location"
                                type="text"
                                value={data.location}
                                onChange={(e) => setData('location', e.target.value)}
                                placeholder="41.311151,69.279737"
                            />
                            <InputError message={errors.location} />
                            <p className="text-xs text-muted-foreground">
                                Masalan: 41.311151,69.279737 (vergul bilan ajratilgan)
                            </p>
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
                            {processing ? 'Saqlanmoqda...' : branch ? 'Saqlash' : 'Qo\'shish'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
