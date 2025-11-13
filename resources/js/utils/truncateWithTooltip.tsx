import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

interface TruncateWithTooltipProps {
    text: string;
    maxLength?: number;
}

export function TruncateWithTooltip({
    text,
    maxLength = 50,
}: TruncateWithTooltipProps) {
    if (!text) return <span>-</span>;

    const shouldTruncate = text.length > maxLength;
    const displayText = shouldTruncate
        ? text.substring(0, maxLength) + '...'
        : text;

    if (!shouldTruncate) {
        return <span>{text}</span>;
    }

    return (
        <Tooltip>
            <TooltipTrigger asChild>
                <span className="cursor-help">{displayText}</span>
            </TooltipTrigger>
            <TooltipContent className="max-w-md break-words">
                <p className="whitespace-pre-wrap">{text}</p>
            </TooltipContent>
        </Tooltip>
    );
}

// Helper function for backward compatibility
export function truncateWithTooltip(text: string, maxLength: number = 50) {
    return <TruncateWithTooltip text={text} maxLength={maxLength} />;
}
