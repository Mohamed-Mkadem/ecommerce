<script setup>
import AboutSection from "@/js/Components/FrontEnd/AboutSection.vue";

import section1Image from "@/assets/images/baklawa_fekia.webp";
import section2Image from "@/assets/images/varié_spécial.webp";

const section1Content = {
    img: section1Image,
    en: {
        title: "Authentic Tunisian Heritage",
        description:
            "<p>Welcome to <b>'Boughanmi Pâtisserie'</b>, the place where the essence of Tunisian tradition meets the elegance of unique flavors. Founded out of love and passion for preserving Tunisian sweets, our store offers a one-of-a-kind experience that blends cultural heritage with top-notch quality.</p><p>We specialize in crafting the finest baklawa and other traditional Tunisian pastries, using carefully selected natural ingredients to ensure an unforgettable taste experience. At <b>'Boughanmi Pâtisserie'</b>, we offer more than just a product, we invite you on a journey through timeless flavors, where craftsmanship and authenticity come together to delight your refined palate.</p>",
    },
    ar: {
        title: "تراث تونسي أصيل",
        description:
            "<p>مرحبًا بكم في <b>حلويات البوغانمي</b>، المكان الذي يجمع بين عبق التراث التونسي وأصالة النكهات التقليدية. تأسس متجرنا على حبنا وشغفنا بتقديم الحلويات التونسية التي تحمل في طياتها ذكريات الأجيال ونكهة الوطن. نحن متخصصون في تحضير أرقى أنواع البقلاوة والعديد من الحلويات التونسية التقليدية الأخرى، باستخدام مكونات طبيعية عالية الجودة، لضمان تجربة لا تُنسى لكل قطعة.</p><p>في <b> حلويات البوغانمي</b>، نقدم لعملائنا أكثر من مجرد منتج؛ نحن نقدم رحلة عبر الزمن والنكهات، حيث تمتزج الحرفية مع الأصالة لإرضاء ذوقكم الراقي. هدفنا هو نشر تراثنا الغني وتقديمه بأبهى صورة في كل مناسبة وجعل كل لحظة مع حلويات البوغانمي ذكرى سعيدة.</p>",
    },
    fr: {
        title: "Une Tradition Tunisienne Authentique",
        description:
            "<p>Bienvenue chez <b>'Boughanmi Pâtisserie'</b>, l'endroit où se rencontrent les parfums authentiques de la tradition Tunisienne et le raffinement des saveurs uniques. Fondée par une passion pour la préservation des douceurs Tunisiennes, notre boutique propose une expérience inédite qui allie héritage culturel et qualité supérieure.</p> <p>Nous sommes spécialisés dans la confection de la baklawa et d'autres pâtisseries Tunisiennes traditionnelles, réalisées à partir d'ingrédients naturels soigneusement sélectionnés pour garantir une dégustation inoubliable. Chez <b>'Boughanmi Pâtisserie'</b>, nous ne vous proposons pas qu'un produit, mais une invitation à découvrir des saveurs intemporelles, mêlant artisanat et authenticité pour satisfaire vos goûts les plus raffinés.</p>",
    },
};

const section2Content = {
    img: section2Image,
    en: {
        title: "Our Values",
        description:
            "<p>At <b>'Boughanmi Pâtisserie'</b>, our values and principles are the cornerstone of everything we do. We believe that quality starts with passion, which is why we focus on selecting the finest ingredients and putting our hearts into every creation.</p><p>Our goal is to be a trusted partner for our clients by providing a unique experience that combines authentic flavors with outstanding service. We are committed to preserving Tunisian heritage and passing it on to future generations, while building strong and lasting relationships with our customers through transparency and trust. Your satisfaction is our priority, and every bite of our creations is a celebration of joy and happiness.</p>",
    },
    ar: {
        title: "قيمنا",
        description:
            "<p>في <b>حلويات البوغانمي</b>، قيمنا ومبادؤنا هي حجر الأساس الذي نبني عليه كل خطوة في عملنا. نحن نؤمن أن الجودة تبدأ من الشغف، ولهذا نحرص على اختيار أفضل المكونات وتحضير منتجاتنا بكل حب واهتمام بالتفاصيل. نهدف إلى أن نكون شريكًا موثوقًا لعملائنا، حيث نقدم لهم تجربة فريدة تجمع بين النكهات الأصيلة والخدمة الممتازة.</p><p>نلتزم في <b>حلويات البوغانمي</b> بالحفاظ على التراث التونسي ونقله للأجيال القادمة، ونسعى لبناء علاقات قوية ومستدامة مع عملائنا من خلال الشفافية والثقة. رضاكم هو أولويتنا، ونحن نعمل جاهدين لجعل كل تذوق لمنتجاتنا لحظة من السعادة الحقيقية</p>",
    },
    fr: {
        title: "Nos Valeurs",
        description:
            "<p>Chez <b>'Boughanmi Pâtisserie'</b>, nos valeurs et nos principes sont les fondations de notre succès. Nous croyons que la qualité commence par la passion, et c'est pourquoi nous veillons à sélectionner les meilleurs ingrédients et à mettre tout notre cœur dans la préparation de nos douceurs.</p> <p>Notre objectif est d'être un partenaire de confiance pour nos clients en offrant une expérience unique, combinant saveurs authentiques et service impeccable. Nous nous engageons à préserver le patrimoine Tunisien et à le transmettre aux générations futures, tout en construisant des relations solides et durables avec nos clients grâce à la transparence et à la confiance. Votre satisfaction est notre priorité, et chaque bouchée de nos créations est une célébration de la joie et du plaisir.</p>",
    },
};
</script>

<template>
    <Head :title="$t('About us')" />

    <!-- Page banner -->
    <div
        class="bg-gradient-to-br from-sky-900 via-sky-800 to-primary text-white py-20 px-6 text-center"
    >
        <h1 class="text-4xl font-extrabold tracking-tight">
            {{ $t("About us") }}
        </h1>
    </div>

    <div class="py-16 px-4 md:px-12 max-w-screen-xl mx-auto space-y-20">
        <AboutSection :content="section1Content" />
        <AboutSection :content="section2Content" :reverse="true" />

        <!-- Map -->
        <section>
            <div class="mb-8">
                <p
                    class="text-gold font-bold text-xs tracking-[0.2em] uppercase mb-2"
                >
                    {{ $t("Contact") }}
                </p>
                <h2 class="text-2xl md:text-3xl font-extrabold text-primary">
                    {{ $t("Our Location") }}
                </h2>
                <div class="mt-3 w-12 h-1 rounded-full bg-gold"></div>
            </div>
            <div
                class="rounded-2xl overflow-hidden shadow-lg border border-neutral-100"
            >
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3192.576422902486!2d10.121168129410362!3d36.85261929107798!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12fd3373530de9f5%3A0x7ef395cd30aa32d6!2sBoughanmi%20P%C3%A2tisserie!5e0!3m2!1sen!2stn!4v1741783476810!5m2!1sen!2stn"
                    width="100%"
                    height="450"
                    style="border: 0; display: block"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </section>
    </div>
</template>
