<template>
    <v-container>
      <v-card class="pa-5">
        <v-card-title class="text-h5 text-center">Detalle del Trámite</v-card-title>
  
        <!-- Vuetify Accordion -->
        <v-expansion-panels multiple>
          <v-expansion-panel v-for="(detalles, titulo) in grupoDetalles" :key="titulo">
            <v-expansion-panel-title>
              {{ titulo }}
            </v-expansion-panel-title>
            <v-expansion-panel-text>
              <v-table density="compact">
                <tbody>
                  <tr v-for="detalle in detalles" :key="detalle.id_multinota_seccion_valor">
                    <td>{{ detalle.nombre }}</td>
                    <td>{{ detalle.valor }}</td>
                  </tr>
                </tbody>
              </v-table>
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>
  
        <!-- Botón de volver -->
        <v-btn color="secondary" variant="outlined" class="mt-4" :href="'/tramites'">
          <v-icon left>mdi-arrow-left</v-icon> Volver
        </v-btn>
      </v-card>
    </v-container>
  </template>
  
  <script>
  export default {
    props: {
      detalleTramite: Array, // Recibe los datos desde Laravel
    },
    computed: {
      // Agrupar datos por "titulo"
      grupoDetalles() {
        return this.detalleTramite.reduce((acc, item) => {
          if (!acc[item.titulo]) {
            acc[item.titulo] = [];
          }
          acc[item.titulo].push(item);
          return acc;
        }, {});
      }
    }
  };
  </script>
  