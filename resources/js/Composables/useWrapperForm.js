import { useForm } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";

export function useWrapperForm(products, wrapper = null) {
    const toast = useToast();
    const search = ref("");
    const isEditing = computed(() => wrapper !== null);

    const form = useForm({
        title: wrapper?.title ?? "",
        caption: wrapper?.caption ?? "",
        description: wrapper?.description ?? "",
        is_active: wrapper?.is_active ?? true,
        products: wrapper?.products ? [...wrapper.products] : [],
    });

    const addedProductIds = computed(() =>
        form.products.map((item) => item.product_id),
    );

    const availableProducts = computed(() => {
        return products.filter((product) => {
            if (addedProductIds.value.includes(product.id)) {
                return false;
            }
            if (!search.value.trim()) {
                return true;
            }
            return product.name
                .toLowerCase()
                .includes(search.value.toLowerCase());
        });
    });

    function syncDisplayOrder() {
        form.products.forEach((item, index) => {
            item.display_order = index;
        });
    }

    function addProduct(product) {
        const isFirst = form.products.length === 0;

        form.products.push({
            product_id: product.id,
            name: product.name,
            type: product.type,
            price: product.price,
            main_image_url: product.main_image_url,
            display_order: form.products.length,
            is_default: isFirst,
            free_shipping: false,
        });
    }

    function removeProduct(index) {
        const wasDefault = form.products[index].is_default;
        form.products.splice(index, 1);

        if (wasDefault && form.products.length) {
            form.products[0].is_default = true;
        }

        syncDisplayOrder();
    }

    function setDefault(index) {
        form.products.forEach((item, i) => {
            item.is_default = i === index;
        });
    }

    function moveProduct(index, direction) {
        const target = index + direction;
        if (target < 0 || target >= form.products.length) {
            return;
        }

        const items = [...form.products];
        [items[index], items[target]] = [items[target], items[index]];
        form.products = items;
        syncDisplayOrder();
    }

    watch(
        () => form.products.length,
        () => syncDisplayOrder(),
    );

    function buildPayload() {
        return {
            title: form.title,
            caption: form.caption,
            description: form.description,
            is_active: form.is_active,
            products: form.products.map(
                ({ product_id, display_order, is_default, free_shipping }) => ({
                    product_id,
                    display_order,
                    is_default,
                    free_shipping,
                }),
            ),
        };
    }

    function submitForm() {
        const payload = buildPayload();

        if (isEditing.value) {
            form.transform(() => payload).put(
                route("wrappers.update", wrapper.slug),
                {
                    onSuccess: () => {
                        toast.success(
                            trans("Wrapper.updated_successfully"),
                            getToastOptions(),
                        );
                    },
                },
            );
        } else {
            form.transform(() => payload).post(route("wrappers.store"), {
                onSuccess: () => {
                    toast.success(
                        trans("Wrapper.created_successfully"),
                        getToastOptions(),
                    );
                    // reset the form to initial empty state after creating a wrapper
                    form.reset();
                    search.value = "";
                },
            });
        }
    }

    function typeLabel(type) {
        return type === "pack"
            ? trans("Product.pack")
            : trans("Product.product");
    }

    return {
        form,
        search,
        isEditing,
        availableProducts,
        addProduct,
        removeProduct,
        setDefault,
        moveProduct,
        submitForm,
        typeLabel,
    };
}
