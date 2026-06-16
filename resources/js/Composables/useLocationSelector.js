import { ref, watch, reactive } from "vue";
import axios from "axios";

export function useLocationSelector() {
    const selectedState = ref(null);
    const selectedCity = ref(null);
    const selectedLocality = ref(null);

    const cities = ref([]);
    const localities = ref([]);

    function getCities(state) {
        cities.value = [];
        localities.value = [];
        return axios
            .get(`/states/${state}/cities`)
            .then((data) => data.data)
            .then((data) => {
                cities.value = data;
            });
    }
    function getLocalities(city) {
        return axios
            .get(`/cities/${city}/localities`)
            .then((data) => data.data)
            .then((data) => {
                localities.value = data;
            });
    }

    return {
        selectedState,
        selectedCity,
        selectedLocality,
        cities,
        localities,
        getCities,
        getLocalities,
    };
}
