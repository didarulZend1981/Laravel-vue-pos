
<template>
    <div class="loader-overlay">
        <div class="spinner-container">
            <div class="spinner"></div>
        </div>
    </div>
</template>

<script setup>

</script>

<style scoped>
.loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 9999;
    width: 100vw;
    height: 100vh;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: all;
}

.spinner-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(11, 206, 175, 0.2);
    border-top: 4px solid #36b9cc;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.logo-text {
    font-size: 1.5rem;
    font-weight: 600;
    color: #36b9cc;
    font-family: "Inter", sans-serif;
    letter-spacing: 2px;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

/* Optional: Add a subtle pulse animation to the text */
.logo-text {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 0.8;
    }

    50% {
        opacity: 1;
    }
}
</style>
