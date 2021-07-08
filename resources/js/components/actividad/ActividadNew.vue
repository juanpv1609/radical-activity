<template>
    <div>
        <v-card elevation="2" :loading="loading">
            <v-card-title>
                Registro de actividades
                <v-spacer></v-spacer>
                <v-col cols="3">
                    <template>
                        <v-dialog
                            ref="dialog"
                            v-model="menu"
                            :return-value.sync="fechaHoy"
                            :close-on-content-click="true"
                            width="290px"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="fechaHoy"
                                    label="Fecha de registro"
                                    prepend-icon="mdi-calendar"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                    dense
                                    filed
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                scrollable
                                v-model="fechaHoy"
                                :max="new Date().toISOString().substr(0, 10)"
                                min="1950-01-01"
                                @change="setFecha"
                            >
                            </v-date-picker>
                        </v-dialog>
                    </template>
                </v-col>
                <v-col cols="3">
                    <v-autocomplete
                        :items="horarios"
                        item-text="nombre"
                        item-value="id"
                        v-model="horario"
                        label="Horario"
                        dense
                        return-object
                        :hint="
                            horario.id
                                ? horario.inicio + ' - ' + horario.fin
                                : 'Seleccione un horario'
                        "
                        persistent-hint
                    ></v-autocomplete>
                </v-col>
            </v-card-title>

            <v-card-text>
                <v-row>
                    <v-col cols="12" sm="1">
                        <v-menu
                            ref="inicio"
                            v-model="menu1"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            :return-value.sync="start"
                            transition="scale-transition"
                            offset-y
                            max-width="290px"
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="start"
                                    label="Inicio"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                    :disabled="!horario.id"
                                ></v-text-field>
                            </template>
                            <v-time-picker
                                v-if="menu1"
                                v-model="start"
                                full-width
                                :min="horario.inicio"
                                :max="horario.fin"
                                format="24hr"
                                :allowed-minutes="allowedStep"
                                @click:minute="$refs.inicio.save(start)"
                            ></v-time-picker>
                        </v-menu>
                    </v-col>
                    <v-col cols="12" sm="1">
                        <v-menu
                            ref="fin"
                            v-model="menu2"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            :return-value.sync="end"
                            transition="scale-transition"
                            offset-y
                            max-width="290px"
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="end"
                                    label="Fin"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                    :disabled="!horario.id"
                                ></v-text-field>
                            </template>
                            <v-time-picker
                                v-if="menu2"
                                v-model="end"
                                full-width
                                :min="start"
                                :max="horario.fin"
                                format="24hr"
                                :allowed-minutes="allowedStep"
                                @click:minute="$refs.fin.save(end)"
                            ></v-time-picker>
                        </v-menu>
                    </v-col>
                    <v-col cols="12" sm="2">
                        <v-autocomplete
                            :items="tipoActividades"
                            item-text="descripcion"
                            item-value="id"
                            v-model="tipoActividad"
                            label="Tipo de Actividad"
                            return-object
                            :disabled="!horario.id"
                        ></v-autocomplete>
                    </v-col>
                    <v-col cols="12" sm="6">
                        <v-combobox
                            v-model="modelDescripcion"
                            :items="itemsDescripcion"
                            hide-selected
                            hint="Ingrese la descripción de la actividad y presione Enter o TAB "
                            label="Actividades"
                            multiple
                            persistent-hint
                            small-chips
                            deletable-chips
                            :disabled="!horario.id"
                        >
                        </v-combobox>
                    </v-col>
                    <v-col cols="12" sm="2">
                        <v-btn
                            small
                            color="primary"
                            block
                            large
                            @click="addActivityLine"
                            :disabled="modelDescripcion.length == 0"
                        >
                            agregar
                        </v-btn>
                    </v-col>
                </v-row>
                <v-divider></v-divider>
                <v-row v-if="ActivityLine.length > 0">
                    <v-col cols="12">
                        <v-data-table :headers="headers" :items="ActivityLine">
                            <template v-slot:item="row">
                                <tr>
                                    <!-- <td>{{ row.item.fecha }}</td> -->
                                    <td>
                                        {{ row.item.h_inicio }} ~
                                        {{ row.item.h_fin }}
                                    </td>
                                    <td>
                                        <v-chip
                                            v-for="actividad in row.item
                                                .descripcion"
                                            :key="actividad"
                                            small
                                            label
                                            class="ma-1"
                                        >
                                            {{ actividad }}
                                        </v-chip>
                                    </td>
                                    <td>
                                        <v-edit-dialog
                                            :return-value.sync="
                                                row.item.colaboradores
                                            "
                                            large
                                            persistent
                                            @click:save="
                                                saveColaborador(row.item)
                                            "
                                            @cancel="cancelColaborador"
                                            @open="openColaborador"
                                            @close="closeColaborador"
                                        >
                                            <div>
                                                <v-chip
                                                    v-for="colaborador in row
                                                        .item.colaboradores"
                                                    :key="colaborador"
                                                    class="ma-1"
                                                    x-small
                                                    color="primary"
                                                    pill
                                                >
                                                    {{ colaborador }}
                                                </v-chip>
                                            </div>
                                            <template v-slot:input>
                                                <div class="mt-4 title">
                                                    Colaboradores
                                                </div>
                                                <v-combobox
                                                    v-model="
                                                        row.item.colaboradores
                                                    "
                                                    :items="itemsColaboradores"
                                                    hide-selected
                                                    hint="Ingrese el nombre del colaborador y presione TAB "
                                                    label="Colaboradores"
                                                    multiple
                                                    persistent-hint
                                                    small-chips
                                                    deletable-chips
                                                >
                                                </v-combobox>
                                            </template>
                                        </v-edit-dialog>
                                    </td>
                                    <td>
                                        <v-edit-dialog
                                            :return-value.sync="
                                                row.item.observacion
                                            "
                                            large
                                            persistent
                                            @save="save(row.item)"
                                            @cancel="cancel"
                                            @open="open"
                                            @close="close"
                                        >
                                            <div>
                                                {{ row.item.observacion }}
                                            </div>
                                            <template v-slot:input>
                                                <div class="mt-4 title">
                                                    Observación / Resultados
                                                </div>
                                                <v-textarea
                                                    v-model="
                                                        row.item.observacion
                                                    "
                                                    label="Observación / Resultados"
                                                    clear-icon="mdi-close-circle"
                                                    clearable
                                                    autofocus
                                                    rows="2"
                                                ></v-textarea>
                                            </template>
                                        </v-edit-dialog>
                                    </td>

                                    <td>
                                        <v-checkbox
                                            v-model="row.item.estado"
                                            color="success"
                                            dense
                                        ></v-checkbox>
                                    </td>
                                    <td>
                                        <v-btn
                                            icon
                                            color="error"
                                            x-small
                                            @click="
                                                deleteActivityLine(row.item)
                                            "
                                        >
                                            <v-icon dark>mdi-delete</v-icon>
                                        </v-btn>
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>
                        <v-snackbar
                            v-model="snack"
                            :timeout="3000"
                            :color="snackColor"
                        >
                            {{ snackText }}

                            <template v-slot:action="{ attrs }">
                                <v-btn
                                    v-bind="attrs"
                                    text
                                    @click="snack = false"
                                >
                                    Cerrar
                                </v-btn>
                            </template>
                        </v-snackbar>
                    </v-col>
                </v-row>
                <v-row v-if="ActivityLine.length > 0">
                    <v-col cols="12" sm="3">
                        <v-switch
                            v-model="enviar"
                            color="orange"
                            :label="
                                `${
                                    enviar
                                        ? 'Enviar correo'
                                        : 'NO enviar correo'
                                }`
                            "
                        ></v-switch>
                    </v-col>
                    <v-col cols="12" sm="9">
                        <v-combobox
                            v-model="modelDestinatarios"
                            :items="itemsDestinatarios"
                            hide-selected
                            hint="Ingrese el correo electrónico del destinatario y presione ENTER o TAB para agregar"
                            label="Destinatarios"
                            multiple
                            persistent-hint
                            small-chips
                            deletable-chips
                            :disabled="!enviar"
                            suffix="@gruporadical.com"
                        >
                        </v-combobox>
                    </v-col>
                </v-row>
            </v-card-text>

            <v-card-actions v-if="ActivityLine.length > 0">
                <v-spacer></v-spacer>
                <v-btn block color="primary" @click="enviarActividades"
                    >enviar</v-btn
                >
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
export default {
    data() {
        return {
            start: null,
            end: null,
            menu: false,
            menu1: false,
            menu2: false,
            fechaHoy: null,

            enviar: false,
            //////
            itemsDescripcion: [],
            modelDescripcion: [],
            modelColaboradores: [],
            itemsColaboradores: [],
            modelDestinatarios: [],
            itemsDestinatarios: ['doris.gonzalez','claudio.cortes','cristina.jimenez'],
            snack: false,
            snackColor: "",
            snackText: "",
            /////
            descripcion: null,
            horarios: [],
            tipoActividades: [],
            tipoActividad: {},
            horario: {},
            persona_estudios: [],
            dialogEstudios: false,
            update: false,
            persona: {},
            loading: false,
            titleFormEstudios: null,
            search: "",
            searchEstudios: "",
            valid: true,
            headers: [
                //{ text: "Fecha", value: "fecha" },
                { text: "Inicio ~ Fin", value: "inicio" },
                { text: "Actividades", value: "descripcion" },
                { text: "Colaboradores", value: "colaborador" },
                { text: "Observación / Resultados", value: "observacion" },
                { text: "Completada", value: "estado" },
                { text: "Eliminar", value: "controls", sortable: false }
            ],
            foto: null,
            ActivityLine: [],
            Activity: {}
        };
    },
    watch: {
        modelDescripcion(val) {
            if (val.length > 5) {
                this.$nextTick(() => this.modelDescripcion.pop());
            }
        }
    },
    created() {
        this.initialData();
        var f = new Date();
        this.fechaHoy = f.toISOString().substr(0, 10);
    },
    methods: {
        save(el) {
            this.ActivityLine.observacion = el.observacion;
        },
        cancel() {
            this.snack = true;
            this.snackColor = "error";
            this.snackText = "Cancelado";
        },
        open() {
            this.snack = true;
            this.snackColor = "info";
            this.snackText = "Editar Observación / Resultados";
        },
        close() {
            console.log("Dialog closed");
        },
        saveColaborador(el) {
            this.ActivityLine.colaboradores = el.colaboradores;
        },
        cancelColaborador() {
            this.snack = true;
            this.snackColor = "error";
            this.snackText = "Cancelado";
        },
        openColaborador() {
            this.snack = true;
            this.snackColor = "info";
            this.snackText = "Editar Observación / Resultados";
        },
        closeColaborador() {
            console.log("Dialog closed");
        },
        allowedStep: m => m % 5 === 0,
        initialData() {
            this.axios.get(`/api/horarios`).then(response => {
                this.horarios = response.data;
                console.log(this.horarios);
            });
            this.axios.get(`/api/tipo-actividad`).then(response => {
                this.tipoActividades = response.data;
                console.log(this.horarios);
            });
        },
        setFecha(fechaHoy) {
            this.$refs.dialog.save(fechaHoy);
        },
        addActivityLine() {
            var activityLine = {};
            this.ActivityLine = this.ActivityLine || [];
            console.log(this.modelDescripcion);
            activityLine.tipo_actividad = this.tipoActividad.id;
            activityLine.tipo_actividad_nombre = this.tipoActividad.descripcion;
            activityLine.descripcion = this.modelDescripcion;
            activityLine.observacion = null;
            activityLine.colaboradores = this.modelColaboradores;
            activityLine.h_inicio = this.start;
            activityLine.h_fin = this.end;
            activityLine.estado = true;
            this.ActivityLine.push(activityLine);
            this.start = this.end;
            this.end = null;
            this.descripcion = null;
            this.modelDescripcion = [];
        },
        deleteActivityLine(el) {
            let i = this.ActivityLine.map(data => data.h_inicio).indexOf(
                el.h_inicio
            );
            this.ActivityLine.splice(i, 1);
        },
        enviarActividades() {
            //this.loading = true;
            this.Activity.usuario = this.$store.state.user.id;
            this.Activity.horario = this.horario.id;
            this.Activity.fecha = this.fechaHoy;
            this.Activity.enviaMail = this.enviar;
            this.Activity.destinatarios = this.modelDestinatarios;
            //this.Activity.colaboradores = this.modelColaboradores;
            this.Activity.activities = this.ActivityLine;

            console.log(this.Activity);
            this.axios
                .post("/api/actividades", this.Activity)
                .then(response => {
                    this.Activity = {};
                    this.ActivityLine = [];
                    this.initialData();
                })
                .catch(err => console.log(err))
                .finally(() => (this.loading = false));
        },
        estudios(el) {
            console.log(el);
            this.loading = true;
            this.titleFormEstudios = `${el.nombre} ${el.apellido}`;
            this.foto = !(el.foto == null || el.foto == "")
                ? "data:image/png;base64," + el.foto
                : null;
            //this.usuario.id = el.id;
            this.axios.get(`/api/persona-estudios/${el.id}`).then(response => {
                //this.personas = response.data;

                this.persona_estudios = response.data;
                console.log(this.persona_estudios);
                this.dialogEstudios = true;
                this.loading = false;
            });
        },
        viewDocuments(el) {
            //console.log(el.documentos);
            var arrayDocumentos = el.documentos.split(",");
            console.log(arrayDocumentos);
            arrayDocumentos.forEach(element => {
                window.open("data:image/png;base64," + element, "_blank");
            });
            //this.logo = (!(el.logo==null || el.logo=='')) ? "data:image/png;base64," + el.logo : null;
        },
        deletePerson(el) {
            this.loading = true;
            this.axios.delete(`/api/persona/${el.id}`).then(response => {
                let i = this.personas.map(data => data.id).indexOf(el.id);
                this.personas.splice(i, 1);
                this.loading = false;
            });
        }
    }
};
</script>
