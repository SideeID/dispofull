<style>
    /* Base styles - menggunakan @apply untuk konsistensi dengan Tailwind */
    body {
        @apply font-['Arial',sans-serif] m-0 p-5 text-[12pt];
    }
    
    /* Letter base styles */
    .letter-title {
        @apply text-[14pt] font-bold uppercase text-center my-5;
    }
    .letter-number {
        @apply text-center mb-7;
    }
    .content {
        @apply leading-relaxed;
    }
    .signature {
        @apply mt-12 text-left float-right w-2/5;
    }
    
    /* Tables */
    table.data {
        @apply w-full my-4;
    }
    table.data td {
        @apply py-1.5 align-top;
    }
    table.data td:first-child {
        @apply w-[35%];
    }
    
    /* Footer styles */
    .template-footer-content {
        @apply mt-20 text-[9pt] clear-both;
        page-break-inside: avoid;
    }
    
    @media print {
        .template-footer-content {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
        }
    }
</style>