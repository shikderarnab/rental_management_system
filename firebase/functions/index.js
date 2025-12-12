/**
 * Firebase Cloud Functions for Email Sending
 * 
 * Deploy this function to Firebase:
 * 1. Install Firebase CLI: npm install -g firebase-tools
 * 2. Run: firebase login
 * 3. Run: firebase init functions
 * 4. Copy this file to functions/index.js
 * 5. Run: firebase deploy --only functions
 */

const functions = require('firebase-functions');
const admin = require('firebase-admin');
const nodemailer = require('nodemailer');

admin.initializeApp();

// Configure email transport (using Gmail as example)
const transporter = nodemailer.createTransport({
  service: 'gmail',
  auth: {
    user: functions.config().email.user,
    pass: functions.config().email.password,
  },
});

/**
 * HTTP Cloud Function to send custom emails
 * 
 * POST /sendEmail
 * Body: {
 *   to: "recipient@example.com",
 *   subject: "Email Subject",
 *   body: "Email body (HTML)",
 *   data: {} // Additional data
 * }
 */
exports.sendEmail = functions.https.onRequest(async (req, res) => {
  // CORS handling
  res.set('Access-Control-Allow-Origin', '*');
  res.set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

  if (req.method === 'OPTIONS') {
    res.status(204).send('');
    return;
  }

  if (req.method !== 'POST') {
    res.status(405).send('Method Not Allowed');
    return;
  }

  try {
    const { to, subject, body, data } = req.body;

    if (!to || !subject || !body) {
      res.status(400).json({ error: 'Missing required fields: to, subject, body' });
      return;
    }

    const mailOptions = {
      from: functions.config().email.user,
      to: to,
      subject: subject,
      html: body,
    };

    await transporter.sendMail(mailOptions);

    console.log('Email sent successfully to:', to);

    res.status(200).json({
      success: true,
      message: 'Email sent successfully',
    });
  } catch (error) {
    console.error('Error sending email:', error);
    res.status(500).json({
      success: false,
      error: error.message,
    });
  }
});

/**
 * Triggered when a payment is verified (if using Firestore)
 */
exports.onPaymentVerified = functions.firestore
  .document('payments/{paymentId}')
  .onUpdate(async (change, context) => {
    const before = change.before.data();
    const after = change.after.data();

    if (before.status !== 'verified' && after.status === 'verified') {
      // Send email notification
      const mailOptions = {
        from: functions.config().email.user,
        to: after.tenant_email,
        subject: 'Payment Verified',
        html: `
          <h2>Payment Verified</h2>
          <p>Your payment of ${after.currency} ${after.amount} has been verified.</p>
          <p>Reference: ${after.reference || after.id}</p>
        `,
      };

      await transporter.sendMail(mailOptions);
      console.log('Payment verification email sent to:', after.tenant_email);
    }
  });

