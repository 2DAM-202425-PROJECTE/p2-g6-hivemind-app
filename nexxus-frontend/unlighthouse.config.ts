/// <reference types="unlighthouse" />
import { defineConfig } from 'unlighthouse'

export default defineConfig({
  // examplebtn-basic
  site: 'localhost:3000',
  scanner: {
    exclude: ['/private-zone/*']
  },
  debug: true,
  auth: {
    email: 'admin@example.com',
    password: 'admin',
  },
})
