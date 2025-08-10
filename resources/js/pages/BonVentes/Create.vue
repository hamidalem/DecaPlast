<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ClientModal from '@/Components/ClientModal.vue'; // Import the new modal component

const props = defineProps({
    clients: Array,
    produits: Array,
    depots: Array,
    defaultDate: String,
});

const form = useForm({
    date_bv: props.defaultDate,
    nom_client: '',
    products: [{
        id_prod: '',
        id_depot: '',
        qte_vente: 1,
        prix_vente: 0,
        max_qte: 0
    }],
    montant_verse: 0,
});

const showClientModal = ref(false); // State to control modal visibility

// Get available quantity for a product in selected depot
const getAvailableQuantity = (productId, depotId) => {
    if (!productId || !depotId) return 0;
    const produit = props.produits.find(p => p.id_prod == productId);
    if (!produit) return 0;
    const depot = produit.depots.find(d => d.id_depot == depotId);
    return depot ? depot.pivot.qte_depot : 0;
};

// Update max quantity when product or depot changes
const updateMaxQuantity = (productIndex) => {
    const product = form.products[productIndex];
    if (product.id_prod && product.id_depot) {
        product.max_qte = getAvailableQuantity(product.id_prod, product.id_depot);
        // Reset quantity if it exceeds new max
        if (product.qte_vente > product.max_qte) {
            product.qte_vente = product.max_qte;
        }
    } else {
        product.max_qte = 0;
    }
};

const totalAmount = computed(() => {
    return form.products.reduce((total, product) => {
        return total + (product.qte_vente * product.prix_vente);
    }, 0);
});

const resteAPayer = computed(() => {
    return (totalAmount.value - (form.montant_verse || 0)).toFixed(2);
});

const addProduct = () => {
    form.products.push({
        id_prod: '',
        id_depot: '',
        qte_vente: 1,
        prix_vente: 0,
        max_qte: 0
    });
};

const removeProduct = (index) => {
    if (form.products.length > 1) {
        form.products.splice(index, 1);
    }
};

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        etat_bv: parseFloat(data.montant_verse) >= totalAmount.value ? 'paye' : 'verse'
    })).post('/bon-ventes');
};

const openClientModal = () => {
    showClientModal.value = true;
};

const closeClientModal = () => {
    showClientModal.value = false;
    // You may want to refresh the page or update the clients list here
    // to show the newly added client. A simple page reload works for now.
    // window.location.reload();
};
</script>

<template>
    <Head title="Créer un nouveau bon de vente" />
    <AppLayout>
        <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-6">Créer un Nouveau Bon de Vente</h1>

            <form @submit.prevent="submitForm" class="bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                <!-- Main Form Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date Input -->
                    <div>
                        <label for="date_bv" class="block text-sm font-medium text-gray-700">Date</label>
                        <input
                            type="date"
                            v-model="form.date_bv"
                            id="date_bv"
                            required
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                        >
                    </div>

                    <!-- Client Dropdown and Button -->
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between mb-1">
                            <label for="nom_client" class="block text-sm font-medium text-gray-700">Client</label>
                            <button
                                type="button"
                                @click="openClientModal"
                                class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span class="ml-1">Nouveau client</span>
                            </button>
                        </div>
                        <select
                            v-model="form.nom_client"
                            id="nom_client"
                            required
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                        >
                            <option value="">Sélectionner un client</option>
                            <option v-for="client in clients" :key="client.nom_client" :value="client.nom_client">
                                {{ client.nom_client }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Produits</h3>

                    <div v-for="(product, index) in form.products" :key="index" class="mb-4 p-4 border border-gray-200 rounded-xl bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Product Dropdown -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Produit</label>
                                <select
                                    v-model="product.id_prod"
                                    @change="updateMaxQuantity(index)"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                >
                                    <option value="">Sélectionner un produit</option>
                                    <option v-for="p in produits" :key="p.id_prod" :value="p.id_prod">
                                        {{ p.nom_prod }}
                                    </option>
                                </select>
                            </div>

                            <!-- Depot Dropdown -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Dépôt</label>
                                <select
                                    v-model="product.id_depot"
                                    @change="updateMaxQuantity(index)"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                >
                                    <option value="">Sélectionner un dépôt</option>
                                    <option v-for="d in depots" :key="d.id_depot" :value="d.id_depot">
                                        {{ d.adresse_depot }}
                                    </option>
                                </select>
                            </div>

                            <!-- Quantity Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Quantité (Max: {{ product.max_qte }})
                                </label>
                                <input
                                    type="number"
                                    v-model.number="product.qte_vente"
                                    :max="product.max_qte"
                                    min="1"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                >
                            </div>

                            <!-- Price Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Prix de vente</label>
                                <input
                                    type="number"
                                    v-model.number="product.prix_vente"
                                    min="0"
                                    step="0.01"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                >
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="removeProduct(index)"
                            class="mt-4 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                        >
                            Supprimer
                        </button>
                    </div>

                    <button
                        type="button"
                        @click="addProduct"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors"
                    >
                        Ajouter un produit
                    </button>
                </div>

                <!-- Summary Section -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Montant versé -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Montant versé</label>
                            <input
                                type="number"
                                v-model.number="form.montant_verse"
                                min="0"
                                step="0.01"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                            >
                        </div>

                        <!-- Total -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-700">Total</h3>
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ totalAmount.toFixed(2) }} DA</p>
                        </div>

                        <!-- Reste à payer -->
                        <div class="md:col-span-3">
                            <h3 class="text-sm font-medium text-gray-700">Reste à payer</h3>
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ resteAPayer }} DA</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 mt-8">
                    <Link
                        href="/bon-ventes"
                        class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                    >
                        Annuler
                    </Link>
                    <button
                        type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors"
                        :disabled="form.processing"
                    >
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>

    <!-- Modals -->
    <ClientModal
        :show="showClientModal"
        @close="closeClientModal"
    />
</template>
