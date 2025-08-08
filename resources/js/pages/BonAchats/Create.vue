<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    fournisseurs: Array,
    produits: Array,
    defaultDate: String,
});

const form = useForm({
    date_ba: props.defaultDate,
    nom_fourn: '',
    products: [{ id_prod: '', qte_achat: 1, prix_achat: 0 }],
    montant_verse: 0,
});

const totalAmount = computed(() => {
    return form.products.reduce((total, product) => {
        return total + (product.qte_achat * product.prix_achat);
    }, 0);
});

const resteAPayer = computed(() => {
    return (totalAmount.value - form.montant_verse).toFixed(2);
});

const addProduct = () => {
    form.products.push({ id_prod: '', qte_achat: 1, prix_achat: 0 });
};

const removeProduct = (index) => {
    if (form.products.length > 1) {
        form.products.splice(index, 1);
    }
};

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        etat_ba: data.montant_verse >= totalAmount.value ? 'paye' : 'verse'
    })).post('/bon-achats');
};
</script>

<template>
    <Head title="Créer un nouveau bon d'achat" />
    <AppLayout>
        <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-6">Créer un Nouveau Bon d'Achat</h1>

            <form @submit.prevent="submitForm" class="bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                <!-- Main Form Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date Input -->
                    <div>
                        <label for="date_ba" class="block text-sm font-medium text-gray-700">Date</label>
                        <input
                            type="date"
                            v-model="form.date_ba"
                            id="date_ba"
                            required
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                        >
                    </div>

                    <!-- Fournisseur Dropdown -->
                    <div class="md:col-span-2">
                        <label for="nom_fourn" class="block text-sm font-medium text-gray-700">Fournisseur</label>
                        <select
                            v-model="form.nom_fourn"
                            id="nom_fourn"
                            required
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                        >
                            <option value="">Sélectionner un fournisseur</option>
                            <option v-for="fournisseur in fournisseurs" :key="fournisseur.nom_fourn" :value="fournisseur.nom_fourn">
                                {{ fournisseur.nom_fourn }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Produits</h3>

                    <div v-for="(product, index) in form.products" :key="index" class="mb-4 p-4 border border-gray-200 rounded-xl bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Product Dropdown -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Produit</label>
                                <select
                                    v-model="product.id_prod"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                >
                                    <option value="">Sélectionner un produit</option>
                                    <option v-for="p in produits" :key="p.id_prod" :value="p.id_prod">
                                        {{ p.nom_prod }}
                                    </option>
                                </select>
                            </div>

                            <!-- Quantity Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Quantité</label>
                                <input
                                    type="number"
                                    v-model.number="product.qte_achat"
                                    min="1"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                >
                            </div>

                            <!-- Price Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Prix d'achat</label>
                                <input
                                    type="number"
                                    v-model.number="product.prix_achat"
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
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ totalAmount }} DA</p>
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
                        href="/bon-achats"
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
</template>
