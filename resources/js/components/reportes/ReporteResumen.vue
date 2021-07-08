<template>
    <div>
        <v-card elevation="2" :loading="loading">
            <v-card-title class="d-flex justify-space-between mb-6"
                >Resumen General
            </v-card-title>

            <v-card-text>
                <v-form ref="form" v-model="valid">
                    <v-row>
                        <v-col cols="12" sm="4">
                            <v-menu
                                v-model="menu"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="dateRangeText"
                                        label="Rango de fechas"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                        filled
                                        rounded
                                        dense
                                        single-line
                                        hide-details
                                    ></v-text-field>
                                </template>
                                <v-date-picker v-model="dates" range>
                                </v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col cols="12" sm="4">
                            <v-autocomplete
                                deletable-chips
                                multiple
                                small-chips
                                clearable
                                :items="usuarios"
                                item-text="name"
                                item-value="id"
                                v-model="selectedUsuarios"
                                label="Seleccione una o varios usuarios"
                                dense
                                return-object
                            >
                                <template v-slot:prepend-item>
                                    <v-list-item ripple @click="toggle">
                                        <v-list-item-action>
                                            <v-icon
                                                :color="
                                                    selectedUsuarios.length > 0
                                                        ? 'indigo darken-4'
                                                        : ''
                                                "
                                            >
                                                {{ icon }}
                                            </v-icon>
                                        </v-list-item-action>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                Seleccionar Todo
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-divider class="mt-2"></v-divider>
                                </template>
                            </v-autocomplete>
                        </v-col>
                        <v-col cols="12" sm="4">
                            <v-btn
                                block
                                color="primary"
                                :loading="loadingUpload"
                                dark
                                @click="generarReporte"
                                >Generar Reporte</v-btn
                            >
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loading: false,
            loadingUpload: false,
            dates: [],
            valid: true,
            menu: false,
            usuarios: [],
            selectedUsuarios: [],
            dateRules: [v => !!v || "Date range is required"]
        };
    },
    computed: {
        dateRangeText() {
            return this.dates.join(" ~ ");
        },
        likesAllCertifications () {
        return this.selectedUsuarios.length === this.usuarios.length
      },
      likesSomeCertifications () {
        return this.selectedUsuarios.length > 0 && !this.likesAllCertifications
      },
      icon () {
        if (this.likesAllCertifications) return 'mdi-close-box'
        if (this.likesSomeCertifications) return 'mdi-minus-box'
        return 'mdi-checkbox-blank-outline'
      },
    },
    created() {
        this.initialData();
    },
    methods: {
        toggle() {
            this.$nextTick(() => {
                if (this.likesAllCertifications) {
                    this.selectedUsuarios = [];
                } else {
                    this.selectedUsuarios = this.usuarios.slice();
                }
            });
        },
        initialData() {
            this.axios.get("/api/usuarios-all").then(response => {
                this.usuarios = response.data;

                this.loading = false;
            });
        },
        async generarReporte() {
            this.loading = true;
            this.loadingUpload = true;
            let url;
            await this.axios
                .get(`/api/reporte-contratos/`)
                .then(response => {
                    url = response.config.baseURL + response.config.url;
                })
                .catch(error => console.log(error));

            this.loading = false;
            this.loadingUpload = false;
            this.dates = [];
            this.valid = true;
            window.open(url, "_blank");
        }
    }
};
</script>

<style></style>
