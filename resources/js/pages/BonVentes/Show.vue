<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    bonVente: {
        type: Object,
        required: true
    },
    totalAmount: {
        type: Number,
        required: true
    },
    montantVerse: {
        type: Number,
        required: true
    }
});
</script>

<template>
    <Head :title="`Bon de Vente #${bonVente.n_bv}`" />
    <AppLayout>
        <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Bon de Vente #{{ bonVente.n_bv }}</h1>
                <div class="flex space-x-2">
                    <Link
                        :href="`/bon-ventes/${bonVente.n_bv}/edit`"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-gray-50 bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Modifier
                    </Link>
                    <Link
                        href="/bon-ventes"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                    >
                        Retour
                    </Link>
                </div>
            </div>

            <!-- Bon de Vente Details -->
            <div class="bg-white shadow-lg overflow-hidden sm:rounded-lg mb-6 p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ new Date(bonVente.date_bv).toLocaleDateString() }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">État</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <span :class="{
                                'bg-green-200 text-green-800': bonVente.etat_bv === 'paye',
                                'bg-blue-200 text-blue-800': bonVente.etat_bv === 'verse'
                            }" class="px-2 py-1 rounded-full text-xs font-semibold">
                                {{ bonVente.etat_bv }}
                            </span>
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Client</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ bonVente.client?.nom_client || 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Products Table -->
            <div class="bg-white shadow-lg sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Détails des produits</h3>
                    <div class="overflow-x-auto border border-gray-200 rounded-md">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(product, index) in bonVente.produits" :key="product.id_prod">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ product.nom_prod }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.pivot.qte_vente }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.pivot.prix_vente }} DA</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">{{ (product.pivot.qte_vente * product.pivot.prix_vente) }} DA</td>
                            </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-sm font-bold text-gray-900 uppercase">Montant total</td>
                                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-bold text-gray-900">{{ totalAmount }} DA</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="bg-white shadow-lg sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-sm font-medium text-gray-500">Montant Totale</p>
                    <p class="text-lg font-semibold text-gray-900">{{ totalAmount }} DA</p>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <p class="text-sm font-medium text-gray-500">Montant versé</p>
                    <p class="text-lg font-semibold text-gray-900">{{ montantVerse }} DA</p>
                </div>
                <div class="flex justify-between items-center border-t pt-4 mt-4">
                    <p class="text-lg font-bold text-gray-500">Reste à payer</p>
                    <p class="text-2xl font-extrabold text-gray-900">{{ (totalAmount - montantVerse) }} DA</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
