import { defineStore } from "pinia";
import { ref, computed, watch } from "vue";
import { trackFacebookEvent } from "@/js/Utils/facebook";

export const useCartStore = defineStore("cart", () => {
    const cart = ref(JSON.parse(sessionStorage.getItem("cart")) || []);

    const total = computed(() =>
        cart.value
            .reduce((sum, item) => sum + item.price * item.quantity, 0)
            .toFixed(3),
    );

    const count = computed(() => cart.value.length);

    const productSubTotal = (product_id) => {
        const product = cart.value.find((item) => item.id === product_id);
        return (product ? product.price * product.quantity : 0).toFixed(3);
    };

    const addToCart = (product, quantity = null) => {
        const existingItem = cart.value.find((item) => item.id === product.id);
        const quantityToAdd = quantity != null ? quantity : 1;

        if (existingItem) {
            existingItem.quantity += quantityToAdd;
        } else {
            cart.value.push({
                ...product,
                quantity: quantityToAdd,
            });
        }

        // Save to session storage
        sessionStorage.setItem("cart", JSON.stringify(cart.value));

        trackAddToCartEvent(product, quantityToAdd);
    };

    const increaseQuantity = (product_id) => {
        const product = cart.value.find((item) => item.id == product_id);

        const updateQuantityBy = product.update_quantity_by;

        product.quantity = product.quantity + updateQuantityBy;

    };
    const decreaseQuantity = (product_id) => {
        const product = cart.value.find((item) => item.id == product_id);
        const updateQuantityBy = product.update_quantity_by;
        if (product.quantity > updateQuantityBy) {
            product.quantity = product.quantity - updateQuantityBy;
        }

    };

    const removeFromCart = (product_id) => {
        cart.value = cart.value.filter((item) => item.id !== product_id);
    };

    const clearCart = () => {
        cart.value = [];
    };

    watch(
        cart,
        (newCart) => {
            sessionStorage.setItem("cart", JSON.stringify(newCart));
        },
        { deep: true },
    );
    const getLocalizedName = (product_id, locale) => {
        let product = cart.value.find((item) => item.id == product_id);
        let translations = product.translations;
        let translation = translations.find(
            (translation) => translation.locale == locale,
        );
        return translation.name;
    };

    const trackAddToCartEvent = (product, quantity) => {
        if (!product?.id) {
            return;
        }

        const price = Number(product.price) || 0;
        const payload = {
            content_ids: [product.id.toString()],
            content_name: product.name,
            content_type: "product",
            currency: "TND",
            value: Number((price * quantity).toFixed(3)),
            num_items: quantity,
            contents: [
                {
                    id: product.id.toString(),
                    quantity,
                    item_price: price,
                },
            ],
        };

        trackFacebookEvent("AddToCart", payload, {
            eventID: `addtocart-${product.id}-${Date.now()}`,
        });
    };
    return {
        cart,
        total,
        count,
        addToCart,
        removeFromCart,
        clearCart,
        getLocalizedName,
        productSubTotal,
        increaseQuantity,
        decreaseQuantity,
    };
});
