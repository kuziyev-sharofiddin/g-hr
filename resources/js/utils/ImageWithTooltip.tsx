import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

interface ImageWithTooltipProps {
    src?: string;
    alt: string;
    className?: string;
}

export function ImageWithTooltip({
    src,
    alt,
    className = 'h-10 w-10 rounded-md object-cover',
}: ImageWithTooltipProps) {
    if (!src) {
        return (
            <div className="flex h-10 w-10 items-center justify-center rounded-md bg-gray-200 dark:bg-gray-700">
                <span className="text-xs text-gray-500">No Image</span>
            </div>
        );
    }

    return (
        <Tooltip>
            <TooltipTrigger asChild>
                <img
                    src={src}
                    alt={alt}
                    className={`cursor-pointer ${className}`}
                />
            </TooltipTrigger>
            <TooltipContent side="right">
                <img
                    src={src}
                    alt={alt}
                    className="h-40 w-40 rounded-lg object-cover"
                />
            </TooltipContent>
        </Tooltip>
    );
}
