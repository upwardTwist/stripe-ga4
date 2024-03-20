
import App from '../src/views/App';

export * from '@stripe/ui-extension-sdk/version';
export const BUILD_TIME = '2024-03-11 14:58:03.1324906 +0500 PKT m=+9.791296701';

export { 
  App	
 };

export default {
  "id": "com.example.stripe-g-4-fe",
  "version": "0.0.1",
  "name": "StripeG4",
  "icon": "",
  "permissions": [],
  "ui_extension": {
    "views": [
      {
        "viewport": "stripe.dashboard.customer.detail",
        "component": "App"
      }
    ],
    "content_security_policy": {
      "connect-src": null,
      "image-src": null,
      "purpose": ""
    }
  }
};
