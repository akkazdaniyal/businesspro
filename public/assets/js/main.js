
"use strict";

document.addEventListener("DOMContentLoaded", () => {

    const purposeFilter = document.getElementById("purposeFilter");
    const locationFilter = document.getElementById("locationFilter");
    const typeFilter = document.getElementById("typeFilter");

    const propertyItems = document.querySelectorAll(".property-item");
    const noResults = document.getElementById("noResults");
    const resultsCount = document.getElementById("resultsCount");

    // Run filters only on the Properties page
    if (!purposeFilter || !locationFilter || !typeFilter) {
        return;
    }

    function filterProperties() {

        const selectedPurpose = purposeFilter.value;
        const selectedLocation = locationFilter.value;
        const selectedType = typeFilter.value;

        let visibleCount = 0;

        propertyItems.forEach((property) => {

            const matchesPurpose =
                selectedPurpose === "all" ||
                property.dataset.purpose === selectedPurpose;

            const matchesLocation =
                selectedLocation === "all" ||
                property.dataset.location === selectedLocation;

            const matchesType =
                selectedType === "all" ||
                property.dataset.type === selectedType;

            const isVisible =
                matchesPurpose &&
                matchesLocation &&
                matchesType;

            property.classList.toggle("d-none", !isVisible);

            if (isVisible) {
                visibleCount++;
            }

        });

        resultsCount.textContent =
            `${visibleCount} ${visibleCount === 1 ? "property" : "properties"} found`;

        noResults.classList.toggle("d-none", visibleCount !== 0);
    }

    purposeFilter.addEventListener("change", filterProperties);
    locationFilter.addEventListener("change", filterProperties);
    typeFilter.addEventListener("change", filterProperties);

    // Display initial results count
    filterProperties();

});
