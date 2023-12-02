"use client"
import React from 'react'
import {Inter} from 'next/font/google'
import './globals.css'
import {QueryClient, QueryClientProvider} from "@tanstack/react-query"

const inter = Inter({subsets: ['latin']})

const queryClient = new QueryClient()

export default function RootLayout({
                                       children,
                                   }: {
    children: React.ReactNode
}) {
    return (
        <QueryClientProvider client={queryClient}>
            <html lang="fr">
            <head>
                <title>Rush 2 Web@cademie By Epitech</title>
                <script defer src="https://cdn.tailwindcss.com"></script>
            </head>
            <body className={inter.className}>{children}</body>
            </html>
        </QueryClientProvider>
    )
}
