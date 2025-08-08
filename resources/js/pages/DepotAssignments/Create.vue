<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    bonAchat: Object,
    depots: Array,
    produit: Object,
});

const form = useForm({
    assignments: [{
        id_prod: props.produit.id_prod,
        nom_prod: props.produit.nom_prod,
        id_depot: '',
        qte_depot: null,
        max_qte: props.produit.remaining_quantity,
        assigned_quantity: props.produit.assigned_quantity,
        qte_achat: props.produit.qte_achat,
    }],
});

const submitForm = () => {
    const validAssignments = form.assignments.filter(a => a.id_depot && a.qte_depot);

    if (validAssignments.length === 0) {
        alert('Veuillez sélectionner au moins un dépôt et une quantité');
        return;
    }

    form.assignments = validAssignments;

    form.post(route('depot-assignments.store', props.bonAchat.n_ba), {
        onSuccess: () => {
            form.reset();
        },
        onError: (errors) => {
            if (errors.assignments) {
                alert('Erreur: ' + errors.assignments);
            }
        }
    });
};

const addDepotAssignment = () => {
    form.assignments.push({
        id_prod: props.produit.id_prod,
        nom_prod: props.produit.nom_prod,
        id_depot: '',
        qte_depot: null,
        max_qte: remainingQuantity(),
        assigned_quantity: props.produit.assigned_quantity,
        qte_achat: props.produit.qte_achat,
    });
};

const removeAssignment = (index) => {
    form.assignments.splice(index, 1);
};

const remainingQuantity = () => {
    const initial = props.produit.remaining_quantity;
    const assigned = form.assignments.reduce((sum, a) => sum + (a.qte_depot || 0), 0);
    return initial - assigned;
};
</script>

<template>
    <Head :title="`Assigner ${produit.nom_prod} - BA #${bonAchat.n_ba}`" />
    <AppLayout>
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    Assigner {{ produit.nom_prod }} aux dépôts - BA #{{ bonAchat.n_ba }}
                </h1>

            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <form @submit.prevent="submitForm" class="p-6 space-y-6">
                    <div class="border-b border-gray-200 pb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ produit.nom_prod }}
                            </h3>
                            <div class="text-sm text-gray-500">
                                Quantité totale: {{ produit.qte_achat }} |
                                Déjà assignée: {{ produit.assigned_quantity }} |
                                Restante: {{ produit.remaining_quantity }}
                            </div>
                        </div>

                        <div v-for="(assignment, index) in form.assignments"
                             :key="index"
                             class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dépôt</label>
                                    <select
                                        v-model="assignment.id_depot"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Sélectionner un dépôt</option>
                                        <option v-for="depot in depots"
                                                :key="depot.id_depot"
                                                :value="depot.id_depot"
                                                :disabled="form.assignments.some(a => a.id_depot === depot.id_depot && a !== assignment)">
                                            {{ depot.nom_depot }} ({{ depot.adresse_depot }})
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Quantité (max: {{ remainingQuantity() + (assignment.qte_depot || 0) }})
                                    </label>
                                    <input
                                        type="number"
                                        v-model.number="assignment.qte_depot"
                                        :max="remainingQuantity() + (assignment.qte_depot || 0)"
                                        min="1"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                    <div class="text-xs text-gray-500 mt-1">
                                        Restant après cet assignement: {{ remainingQuantity() }}
                                    </div>
                                </div>

                                <div class="flex items-end space-x-2">
                                    <button
                                        type="button"
                                        @click="removeAssignment(index)"
                                        class="px-3 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 text-sm"
                                        :disabled="form.assignments.length <= 1"
                                    >
                                        Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="addDepotAssignment"
                            class="mt-2 px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm"
                            :disabled="remainingQuantity() <= 0"
                        >
                            + Ajouter un autre dépôt pour ce produit
                        </button>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                        <Link
                            :href="route('bon-achats.show', bonAchat.n_ba)"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Annuler
                        </Link>
                        <button
                            type="submit"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-gray-50 bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors"
                            :disabled="form.processing || form.assignments.every(a => !a.id_depot || !a.qte_depot)"
                        >
                            <span v-if="form.processing">En cours...</span>
                            <span v-else>Enregistrer les assignations</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
