<template>
  <div>
    <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">
      {{ filter.name }}
    </h3>
    <div class="p-2">
      <v-select
          appendToBody
          :value="value"
          :options="availableResources"
          :placeholder="placeholder"
          :loading="loading"
          label="display"
          track-by="value"
          class="searchable-belongs-to-filter"
          @input="handleChange"
          @reset="clearSelection"
          @search="fetchOptions"
      >
        <div slot="no-options">{{noOptionsLabel}}</div>
      </v-select>
    </div>
  </div>
</template>

<script>
import { PerformsSearches } from "laravel-nova"
import vSelect from 'vue-select'
import storage from '../storage/BelongsToFieldStorage'

export default {
  mixins: [PerformsSearches],
  components: { vSelect },

  props: {
    resourceName: {
      type: String,
      required: true,
    },
    filterKey: {
      type: String,
      required: true,
    },
  },

  data: () => ({
    loading: false
  }),

  methods: {
    getAvailableResources() {
      if (this.search.length >= this.searchFromLength) {
        return storage.fetchAvailableResources(
            this.resourceName,
            this.fieldAttribute,
            {
              params: {
                search: this.search,
              },
            }
        ).then(({data: {resources}}) => {
          this.availableResources = resources;
        }).finally(() => {
          this.loading = false;
        });
      }
    },

    handleChange(resource) {
      this.$store.commit(`${this.resourceName}/updateFilterState`, {
        filterClass: this.filterKey,
        value: resource
      });

      this.$emit("change");
    },

    fetchOptions(search, loading) {
      this.loading = search.length >= this.searchFromLength;
      this.performSearch(search);
    }
  },

  computed: {
    filter() {
      return this.$store.getters[`${this.resourceName}/getFilter`](
          this.filterKey
      );
    },

    fieldAttribute() {
      return this.filter.fieldAttribute;
    },

    value() {
      return this.filter.currentValue;
    },

    noOptionsLabel: function () {
      if (this.search.length < this.searchFromLength) {
        return this.filter.emptySearchLabel || this.__('Enter at least :attribute letters to start search.', { "attribute": this.searchFromLength.toString() });
      }
      return this.filter.noOptionsLabel || this.__('Sorry, no matching options.');
    },

    placeholder: function () {
      return this.filter.placeholder || this.__('Select option');
    },

    searchFromLength() {
      return this.filter.searchFromLength || 2;
    },
  },
};
</script>
