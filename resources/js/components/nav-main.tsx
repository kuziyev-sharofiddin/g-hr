import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { ChevronRightIcon } from 'lucide-react';
import { useState } from 'react';

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const page = usePage();
    const [openItems, setOpenItems] = useState<Record<string, boolean>>({});

    // Check if any sub-item is active
    const isGroupActive = (item: NavItem) => {
        if (!item.items) return false;
        return item.items.some((subItem) => {
            if (!subItem.href) return false;
            const href = typeof subItem.href === 'string' ? subItem.href : subItem.href.url;
            return page.url.startsWith(href);
        });
    };

    const toggleOpen = (itemTitle: string, isOpen: boolean) => {
        setOpenItems((prev) => ({
            ...prev,
            [itemTitle]: isOpen,
        }));
    };

    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel>Platform</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item) =>
                    item.items && item.items.length > 0 ? (
                        <Collapsible
                            key={item.title}
                            asChild
                            defaultOpen={isGroupActive(item)}
                            onOpenChange={(isOpen) => toggleOpen(item.title, isOpen)}
                        >
                            <SidebarMenuItem>
                                <CollapsibleTrigger asChild>
                                    <SidebarMenuButton
                                        tooltip={{ children: item.title }}
                                    >
                                        {item.icon && <item.icon />}
                                        <span>{item.title}</span>
                                        <ChevronRightIcon
                                            className={`ml-auto h-4 w-4 transition-transform duration-200 ${
                                                openItems[item.title] ? 'rotate-90' : ''
                                            }`}
                                        />
                                    </SidebarMenuButton>
                                </CollapsibleTrigger>
                                <CollapsibleContent>
                                    <SidebarMenuSub>
                                        {item.items.map((subItem) => (
                                            <SidebarMenuSubItem
                                                key={subItem.title}
                                            >
                                                <SidebarMenuSubButton
                                                    asChild
                                                    isActive={
                                                        subItem.href &&
                                                        page.url.startsWith(
                                                            typeof subItem.href ===
                                                                'string'
                                                                ? subItem.href
                                                                : subItem.href
                                                                      .url,
                                                        )
                                                    }
                                                >
                                                    <Link
                                                        href={
                                                            typeof subItem.href === 'string'
                                                                ? subItem.href
                                                                : (subItem.href?.url || '#')
                                                        }
                                                    >
                                                        <span>
                                                            {subItem.title}
                                                        </span>
                                                    </Link>
                                                </SidebarMenuSubButton>
                                            </SidebarMenuSubItem>
                                        ))}
                                    </SidebarMenuSub>
                                </CollapsibleContent>
                            </SidebarMenuItem>
                        </Collapsible>
                    ) : (
                        <SidebarMenuItem key={item.title}>
                            <SidebarMenuButton
                                asChild
                                isActive={
                                    item.href &&
                                    page.url.startsWith(
                                        typeof item.href === 'string'
                                            ? item.href
                                            : item.href.url,
                                    )
                                }
                                tooltip={{ children: item.title }}
                            >
                                <Link
                                    href={
                                        typeof item.href === 'string'
                                            ? item.href
                                            : (item.href?.url || '#')
                                    }
                                >
                                    {item.icon && <item.icon />}
                                    <span>{item.title}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    ),
                )}
            </SidebarMenu>
        </SidebarGroup>
    );
}
