<template>
    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
        <!-- Rounded Background with Sky Gradient -->
        <rect width="100" height="100" rx="24" fill="url(#salsabila-grad)" />
        <rect x="0.75" y="0.75" width="98.5" height="98.5" rx="23.25" stroke="rgba(255,255,255,0.25)" stroke-width="1.5"/>

        <!-- Subtle drafting grid background lines -->
        <path d="M20 50H80M50 20V80M20 20L80 80M20 80L80 20" stroke="rgba(255,255,255,0.15)" stroke-width="1" stroke-dasharray="3 3"/>
        <circle cx="50" cy="50" r="30" stroke="rgba(255,255,255,0.15)" stroke-width="1"/>

        <!-- Architectural Compass / Drafting Tool -->
        <circle cx="50" cy="28" r="6" fill="#FFFFFF"/>
        <circle cx="50" cy="28" r="2.5" fill="#0284C7"/>
        <path d="M50 34V38" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round"/>
        <!-- Left Leg -->
        <path d="M48 38L32 74" stroke="#FFFFFF" stroke-width="4.5" stroke-linecap="round"/>
        <!-- Right Leg -->
        <path d="M52 38L68 74" stroke="#FFFFFF" stroke-width="4.5" stroke-linecap="round"/>
        <!-- Crossbar / Caliper needle -->
        <path d="M38 58C45 62 55 62 62 58" stroke="#BAE6FD" stroke-width="2.5" stroke-linecap="round"/>
        <!-- Needle tips -->
        <circle cx="32" cy="74" r="2.5" fill="#BAE6FD"/>
        <circle cx="68" cy="74" r="2.5" fill="#BAE6FD"/>

        <defs>
            <linearGradient id="salsabila-grad" x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse">
                <stop stop-color="#38BDF8"/>
                <stop offset="1" stop-color="#0284C7"/>
            </linearGradient>
        </defs>
    </svg>
</template>
