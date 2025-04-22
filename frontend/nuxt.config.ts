// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  css: ['bootstrap/dist/css/bootstrap.min.css'],
  plugins: ['~/plugins/bootstrap.client.ts'],
  pages: true,
  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8000/api',
      csrfURL: 'http://localhost:8000/sanctum/csrf-cookie'
    }
  }
})
