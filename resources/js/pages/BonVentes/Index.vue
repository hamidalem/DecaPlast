<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    bonVentes: Array,
    clients: Array
});

// Search and filter functionality
const searchQuery = ref('');
const orderBy = ref('n_bv');
const orderDirection = ref('asc');
const filterEtat = ref('all');

// Computed properties for filtered and sorted data
const filteredBonVentes = computed(() => {
    let result = [...props.bonVentes];

    // Apply search filter to multiple fields
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(bv =>
            bv.n_bv.toString().includes(query) ||
            bv.client?.nom_client?.toLowerCase().includes(query) ||
            bv.produits?.some(p => p.nom_prod.toLowerCase().includes(query))
        );
    }

    // Apply state filter
    if (filterEtat.value !== 'all') {
        result = result.filter(bv => bv.etat_bv === filterEtat.value);
    }

    // Apply sorting
    result.sort((a, b) => {
        let comparison = 0;

        if (orderBy.value === 'n_bv') {
            comparison = a.n_bv - b.n_bv;
        } else if (orderBy.value === 'date_bv') {
            comparison = new Date(a.date_bv) - new Date(b.date_bv);
        } else if (orderBy.value === 'total_amount') {
            comparison = a.total_amount - b.total_amount;
        } else if (orderBy.value === 'reste_a_payer') {
            comparison = (a.reste_a_payer || 0) - (b.reste_a_payer || 0);
        }

        return orderDirection.value === 'asc' ? comparison : -comparison;
    });

    return result;
});

// Toggle sort direction
const toggleSort = (field) => {
    if (orderBy.value === field) {
        orderDirection.value = orderDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        orderBy.value = field;
        orderDirection.value = 'asc';
    }
};

// Sort indicator
const sortIndicator = (field) => {
    if (orderBy.value === field) {
        return orderDirection.value === 'asc' ? '↑' : '↓';
    }
    return '';
};
</script>

<template>
    <Head title="Bons de Vente" />
    <AppLayout>
        <div class="container mx-auto p-4 sm:p-6 lg:p-8">
            <!-- Header and Add Button -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Liste des Bons de Vente</h1>
                <Link
                    href="/bon-ventes/create"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-gray-50 bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Ajouter un Bon
                </Link>
            </div>

            <!-- Filters and Search -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search Bar -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                        <input
                            type="text"
                            id="search"
                            v-model="searchQuery"
                            placeholder="Rechercher par numéro, client ou produit..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-900 focus:border-gray-900"
                        />
                    </div>

                    <!-- Order By -->
                    <div>
                        <label for="orderBy" class="block text-sm font-medium text-gray-700 mb-1">Trier par</label>
                        <div class="flex">
                            <select
                                id="orderBy"
                                v-model="orderBy"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md shadow-sm focus:outline-none focus:ring-gray-900 focus:border-gray-900"
                            >
                                <option value="n_bv">Numéro BV</option>
                                <option value="date_bv">Date</option>
                                <option value="total_amount">Montant Total</option>
                                <option value="reste_a_payer">Reste à Payer</option>
                            </select>
                            <button
                                @click="orderDirection = orderDirection === 'asc' ? 'desc' : 'asc'"
                                class="px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-100 hover:bg-gray-200"
                            >
                                {{ orderDirection === 'asc' ? '↑' : '↓' }}
                            </button>
                        </div>
                    </div>

                    <!-- Filter by State -->
                    <div>
                        <label for="filterEtat" class="block text-sm font-medium text-gray-700 mb-1">Filtrer par état</label>
                        <select
                            id="filterEtat"
                            v-model="filterEtat"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-900 focus:border-gray-900"
                        >
                            <option value="all">Tous les états</option>
                            <option value="paye">Payé</option>
                            <option value="verse">Versé</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white shadow-lg overflow-hidden sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <!-- Table Header -->
                        <thead class="bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider cursor-pointer" @click="toggleSort('n_bv')">
                                N° Bon Vente {{ sortIndicator('n_bv') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider cursor-pointer" @click="toggleSort('date_bv')">
                                Date {{ sortIndicator('date_bv') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">
                                État
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">
                                Client
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">
                                Produits
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider cursor-pointer" @click="toggleSort('reste_a_payer')">
                                Reste a Payer {{ sortIndicator('reste_a_payer') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider cursor-pointer" @click="toggleSort('total_amount')">
                                Total {{ sortIndicator('total_amount') }}
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="(bv, index) in filteredBonVentes"
                            :key="bv.n_bv"
                            :class="{ 'bg-gray-50': index % 2 === 1 }"
                            class="hover:bg-gray-100 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ bv.n_bv }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ new Date(bv.date_bv).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <span :class="{
                                    'bg-green-200 text-green-800': bv.etat_bv === 'paye',
                                    'bg-blue-200 text-blue-800': bv.etat_bv === 'verse'
                                }" class="px-2 py-1 rounded-full text-xs font-semibold">
                                    {{ bv.etat_bv }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ bv.client?.nom_client || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-normal text-sm text-gray-700">
                                <div v-for="product in bv.produits" :key="product.id_prod">
                                    - {{ product.nom_prod }} <strong> (x {{ product.pivot.qte_vente }})</strong>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">
                                {{ (bv.reste_a_payer || 0).toFixed(2) }} DA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">
                                {{ bv.total_amount.toFixed(2) }} DA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <Link :href="`/bon-ventes/${bv.n_bv}`" title="Voir Bon de Vente" class="text-gray-600 hover:text-gray-900">
                                        <span class="sr-only">View</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </Link>
                                    <Link :href="`/bon-ventes/${bv.n_bv}/edit`" title="Modifier Bon de Vente" class="text-gray-600 hover:text-gray-900">
                                        <span class="sr-only">Edit</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </Link>
                                    <Link :href="`/bon-ventes/${bv.n_bv}`" method="delete" as="button" title="Supprimer Bon de Vente" class="text-gray-600 hover:text-gray-900">
                                        <span class="sr-only">Delete</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </Link>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredBonVentes.length === 0">
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                Aucun bon de vente trouvé
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
