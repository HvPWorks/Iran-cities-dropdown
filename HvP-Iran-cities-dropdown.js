jQuery(document).ready(function($) {
    // این نگاشت بین کد استان ووکامرس و province_id در دیتابیس توئه
const provinceMap = {
'EAZ': 103, // آذربایجان شرقی
'WAZ': 104, // آذربایجان غربی
'ADL': 124, // اردبیل
'ESF': 110, // اصفهان
'ABZ': 130, // البرز
'ILM': 116, // ایلام
'BHR': 118, // بوشهر
'THR': 123, // تهران
'CHB': 114, // چهارمحال و بختیاری
'SKH': 129, // خراسان جنوبی
'RKH': 109, // خراسان رضوی
'NKH': 128, // خراسان شمالی
'KHZ': 106, // خوزستان
'ZJN': 119, // زنجان
'SBN': 111, // سیستان و بلوچستان
'SMN': 120, // سمنان
'FRS': 107, // فارس
'GZN': 126, // قزوین
'QHM': 125, // قم
'KRD': 112, // کردستان
'KRN': 108, // کرمان
'KRH': 105, // کرمانشاه
'KBD': 117, // کهگیلویه و بویراحمد
'GIL': 101, // گیلان
'GLS': 127, // گلستان
'LRS': 115, // لرستان
'MZN': 102, // مازندران
'MKZ': 100, // مرکزی
'HRZ': 122, // هرمزگان
'HDN': 113, // همدان
'YZD': 121  // یزد
};

    function updateCities(stateSelector, citySelector) {
        const selectedState = $(stateSelector).val();
        const provinceId = provinceMap[selectedState];

        console.log("Selected State:", selectedState);
        console.log("Mapped province_id:", provinceId);

        const cityField = $(citySelector);
        cityField.empty().append('<option value="">در حال بارگذاری شهرها...</option>');

        if (!provinceId) {
            cityField.empty().append('<option value="">لطفاً ابتدا استان را انتخاب کنید</option>');
            return;
        }

        // citiesData یک آرایه است، باید فیلتر کنیم شهرهای متعلق به این province_id رو
        const cities = iran_data.counties.filter(function(city) {
            return city.province_id === provinceId;
        });

        if (!cities.length) {
            console.log("No cities found for this state.");
            cityField.empty().append('<option value="">شهری یافت نشد</option>');
            return;
        }

        cityField.empty().append('<option value="">انتخاب شهر</option>');
        cities.forEach(function(city) {
            cityField.append('<option value="' + city.name + '">' + city.name + '</option>');
        });
    }

    // اتصال به فیلدهای استان
    $('body').on('change', '#billing_state', function() {
        updateCities('#billing_state', '#billing_city');
    });

    $('body').on('change', '#shipping_state', function() {
        updateCities('#shipping_state', '#shipping_city');
    });

    // بارگذاری اولیه
    if ($('#billing_state').val()) {
        updateCities('#billing_state', '#billing_city');
    }

    if ($('#shipping_state').val()) {
        updateCities('#shipping_state', '#shipping_city');
    }
});
