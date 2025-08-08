<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    bonAchat: {
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
    <Head :title="`Bon d'achat #${bonAchat.n_ba}`" />
    <AppLayout>
        <div class="max-w-5xl mx-auto p-4 sm:p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Bon d'achat #{{ bonAchat.n_ba }}</h1>
                <div class="flex space-x-2">
                    <a
                        :href="`/bon-achats/${bonAchat.n_ba}/export-pdf`"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-gray-50 bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors ml-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                        </svg>
                        Exporter PDF
                    </a>
                    <Link
                        :href="`/bon-achats/${bonAchat.n_ba}/edit`"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-gray-50 bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Modifier
                    </Link>
                    <Link
                        href="/bon-achats"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                    >
                        Retour
                    </Link>
                </div>
            </div>

            <!-- Bon Achat Details -->
            <div class="bg-white shadow-lg overflow-hidden sm:rounded-lg mb-6 p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ new Date(bonAchat.date_ba).toLocaleDateString() }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">État</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <span :class="{
                                'bg-green-200 text-green-800': bonAchat.etat_ba === 'paye',
                                'bg-blue-200 text-blue-800': bonAchat.etat_ba === 'verse'
                            }" class="px-2 py-1 rounded-full text-xs font-semibold">
                                {{ bonAchat.etat_ba }}
                            </span>
                        </dd>

                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Fournisseur</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ bonAchat.fournisseur?.nom_fourn || 'N/A' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Statut</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                        <span :class="{
      'bg-gray-200 text-gray-800': bonAchat.statut_depot === 'non_assigné',
      'bg-yellow-200 text-yellow-800': bonAchat.statut_depot === 'partiellement_assigné',
      'bg-green-200 text-green-800': bonAchat.statut_depot === 'assigné'
    }" class="px-2 py-1 rounded-full text-xs font-semibold">
      {{ bonAchat.statut_depot }}
    </span>
                        </dd>
                    </div>
                </dl>
            </div>

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
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Assignation</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(product, index) in bonAchat.produits" :key="product.id_prod">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ product.nom_prod }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.pivot.qte_achat }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.pivot.prix_achat }} DA</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                                    {{ (product.pivot.qte_achat * product.pivot.prix_achat) }} DA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span :class="{
                                        'bg-gray-200 text-gray-800': product.pivot.assigned_quantity === 0,
                                        'bg-yellow-200 text-yellow-800': product.pivot.assigned_quantity > 0 && product.pivot.assigned_quantity < product.pivot.qte_achat,
                                        'bg-green-200 text-green-800': product.pivot.assigned_quantity >= product.pivot.qte_achat
                                    }" class="px-2 py-1 rounded-full text-xs font-semibold">
                                        {{ product.pivot.assigned_quantity === 0 ? 'Non assigné' :
                                        product.pivot.assigned_quantity >= product.pivot.qte_achat ? 'assigné' :
                                            'Partiellement assigné' }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Assigné: {{ product.pivot.assigned_quantity }}/{{ product.pivot.qte_achat }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link
                                        :href="route('depot-assignments.create', { bonAchat: bonAchat.n_ba, produit: product.id_prod })"
                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-gray-50 bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors"
                                    >
                                        Assigner
                                    </Link>
                                    <Link
                                        :href="route('depot-assignments.edit', { bonAchat: bonAchat.n_ba, produit: product.id_prod })"
                                        class="inline-flex items-center ml-2 px-2 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-gray-50 bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors"
                                    >
                                        Modifier
                                    </Link>
                                </td>
                            </tr>
                            </tbody>
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
