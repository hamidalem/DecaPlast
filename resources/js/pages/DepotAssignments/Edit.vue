<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    bonAchat: Object,
    depots: Array,
    produit: Object,
});

// Calculate the total remaining quantity from the initial product props
const initialRemainingQuantity = computed(() => props.produit.qte_achat - props.produit.assigned_quantity);

// Initialize the form with existing assignments, or a single empty one if none exist
const form = useForm({
    assignments: props.produit.initialAssignments && props.produit.initialAssignments.length > 0
        ? props.produit.initialAssignments.map(assignment => ({
            id_prod: assignment.id_prod,
            nom_prod: assignment.nom_prod,
            id_depot: assignment.id_depot,
            qte_depot: assignment.qte_depot,
            // Keep track of the original quantity to allow changes without exceeding the initial total
            original_qte: assignment.qte_depot,
        }))
        : [{
            id_prod: props.produit.id_prod,
            nom_prod: props.produit.nom_prod,
            id_depot: '',
            qte_depot: null,
            original_qte: 0,
        }],
});

const submitForm = () => {
    // Filter out any assignments that are incomplete
    const validAssignments = form.assignments.filter(a => a.id_depot && a.qte_depot > 0);

    if (validAssignments.length === 0) {
        alert('Veuillez sélectionner au moins un dépôt et une quantité');
        return;
    }

    form.assignments = validAssignments;

    form.put(route('depot-assignments.update', {
        bonAchatId: props.bonAchat.n_ba,
        produit: props.produit.id_prod
    }), {
        onSuccess: () => {
            // After successful update, reset the form to the new state
            // This is a simple way to refresh the view
            form.reset();
        },
        onError: (errors) => {
            if (errors.assignments) {
                alert('Erreur: ' + errors.assignments);
            } else {
                alert('Une erreur est survenue lors de l\'enregistrement.');
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
        original_qte: 0,
    });
};

const removeAssignment = (index) => {
    form.assignments.splice(index, 1);
};

const totalAssignedInForm = computed(() => {
    return form.assignments.reduce((sum, a) => sum + (a.qte_depot || 0), 0);
});

// Calculate the remaining quantity based on the initial remaining amount and the new quantities in the form
const remainingQuantity = computed(() => {
    const totalOriginalInForm = form.assignments.reduce((sum, a) => sum + (a.original_qte || 0), 0);
    const difference = totalAssignedInForm.value - totalOriginalInForm;
    return initialRemainingQuantity.value - difference;
});
</script>

<template>
    <Head :title="`Modifier ${produit.nom_prod} - BA #${bonAchat.n_ba}`" />
    <AppLayout>
        <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    Modifier les assignations de {{ produit.nom_prod }}
                </h1>
            </div>

            <div class="bg-white shadow-lg sm:rounded-lg">
                <form @submit.prevent="submitForm" class="p-6 space-y-6">
                    <div class="border-b border-gray-200 pb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ produit.nom_prod }}
                            </h3>
                            <div class="text-sm text-gray-500">
                                Quantité totale: {{ produit.qte_achat }} |
                                Déjà assignée: {{ produit.assigned_quantity }} |
                                Restante: {{ initialRemainingQuantity }}
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
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900"
                                    >
                                        <option value="">Sélectionner un dépôt</option>
                                        <option v-for="depot in depots"
                                                :key="depot.id_depot"
                                                :value="depot.id_depot"
                                                :disabled="form.assignments.some(a => a.id_depot === depot.id_depot && a !== assignment)">
                                            ({{ depot.adresse_depot }})
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Quantité (max: {{ remainingQuantity + assignment.original_qte }})
                                    </label>
                                    <input
                                        type="number"
                                        v-model.number="assignment.qte_depot"
                                        :max="remainingQuantity + assignment.original_qte"
                                        min="0"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900"
                                    >
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
                            :disabled="remainingQuantity <= 0"
                        >
                            + Ajouter un autre dépôt pour ce produit
                        </button>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                        <Link
                            :href="route('bon-achats.show', bonAchat.n_ba)"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                        >
                            Annuler
                        </Link>
                        <button
                            type="submit"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-gray-50 bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors"
                            :disabled="form.processing || form.assignments.every(a => !a.id_depot || !a.qte_depot)"
                        >
                            <span v-if="form.processing">En cours...</span>
                            <span v-else>Modifier les assignations</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
