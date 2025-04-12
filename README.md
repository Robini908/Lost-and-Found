<p align="center"><img src="./public/images/logo.png" width="400" alt="Lost & Found System Logo"></p>

<p align="center">
<a href="https://github.com/your-username/lost-found/actions"><img src="https://github.com/your-username/lost-found/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://github.com/your-username/lost-found/actions"><img src="https://github.com/your-username/lost-found/workflows/static-analysis/badge.svg" alt="Static Analysis"></a>
<a href="https://github.com/your-username/lost-found/releases"><img src="https://img.shields.io/github/v/release/your-username/lost-found" alt="Latest Release"></a>
<a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-blue.svg" alt="License"></a>
</p>

# Lost & Found System

A comprehensive platform that connects people with their lost possessions through intelligent matching algorithms, secure communication, and streamlined recovery workflows.

## 📊 Demo & Screenshots

<details>
<summary>Click to view screenshots</summary>

### Dashboard Overview
![Dashboard](docs/screenshots/dashboard.png)

### Item Reporting Interface
![Report Item](docs/screenshots/report-item.png)

### Matching System
![Matching](docs/screenshots/matching.png)

### User Communication
![Messages](docs/screenshots/messages.png)

</details>

🔗 [Live Demo](https://demo.lostandfound.com) (Username: `demo` | Password: `password`)

## ✨ Features

- **Intelligent Item Matching**: Advanced algorithms match lost items with found items using text similarity, location proximity, and AI-powered pattern recognition
- **Secure Communication**: Anonymous, in-app messaging between item finders and owners
- **Multi-step Verification**: Robust claim verification system ensures legitimate ownership 
- **Reward System**: Points-based incentives encourage honest returns
- **Mobile Responsive**: Full functionality across all devices
- **Modern UI**: User-friendly interface built with Tailwind CSS and Alpine.js
- **Role-Based Access**: Different permission levels for users, moderators, and administrators
- **Analytics Dashboard**: Comprehensive statistics on recovery rates and system usage

## 🚀 Quick Start

For those who want to try out the system quickly, we provide a Docker Compose setup:

```bash
# Clone the repository
git clone https://github.com/your-username/lost-found.git
cd lost-found

# Start with Docker Compose (includes all dependencies)
docker-compose up -d

# Open in your browser
xdg-open http://localhost:8000 || open http://localhost:8000
```

## 🛠️ System Requirements

- PHP >= 8.1
- MySQL >= 8.0
- Composer >= 2.0
- Node.js >= 16
- NPM >= 8

## 📥 Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/your-username/lost-found.git
   cd lost-found
   ```

2. **Install dependencies**

   ```bash
   composer install
   npm install
   ```

3. **Environment setup**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure your environment**

   Edit `.env` file to set up:
   - Database connection
   - Mail service
   - Third-party APIs (Twilio, OpenAI, Google Maps)
   - Storage service (S3 or local)

5. **Run migrations and seed database**

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**

   ```bash
   npm run build
   ```

7. **Start the development server**

   ```bash
   php artisan serve
   ```

8. **Visit your application at http://localhost:8000**

## ⚙️ Configuration

### External Services

The system relies on several external services that need to be configured:

- **Twilio**: For SMS and WhatsApp notifications
- **OpenAI**: For intelligent text matching
- **Google Maps API**: For location services
- **AWS S3**: For file storage (optional)
- **OAuth Providers**: For social authentication (optional)

Configuration instructions for each service can be found in the [detailed documentation](./docs/configuration.md).

### Scheduled Tasks

Set up a cron job on your server to run Laravel's scheduler:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

This handles:
- Automatic expiration of old listings
- Match suggestion generation
- Statistics compilation
- Cleanup tasks

## 🏗️ Architecture

The Lost & Found System follows a modern Laravel architecture with:

- **MVC Pattern**: Clean separation of concerns
- **Repository Pattern**: For data access abstraction
- **Service Layer**: For business logic encapsulation
- **Command Bus**: For background processing
- **Event-driven Components**: For real-time features

A comprehensive architectural overview is available in [our documentation](./docs/architecture.md).

## 📘 API Documentation

The system provides a RESTful API for integration with other platforms. API documentation is available:

- [API Reference](./docs/api.md)
- [Swagger UI](http://localhost:8000/api/documentation) (when running locally)

## 🧪 Testing

The system includes comprehensive test coverage:

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage report
php artisan test --coverage
```

## 🚢 Deployment

Deployment guides are available for:

- [Comprehensive Deployment Guide](./docs/deployment.md) - Detailed instructions for all deployment scenarios
- [Docker Deployment](./docs/deployment.md#docker-deployment) - Quick deployment using Docker
- [Traditional Server Deployment](./docs/deployment.md#traditional-server-deployment) - Step-by-step server setup
- [Cloud Service Providers](./docs/deployment.md#cloud-provider-deployment) - AWS and Digital Ocean deployment

## 🗺️ Project Roadmap

We are continuously improving the Lost & Found System. Here's what's coming:

### Q3 2023
- Mobile application for iOS and Android
- Image recognition for improved matching
- Integration with more OAuth providers

### Q4 2023
- Blockchain-based verification for high-value items
- Global location database integration
- Multi-language support

### Q1 2024
- Enterprise features for large organizations
- Advanced analytics with machine learning
- Public API platform for third-party integrations

## ❓ FAQ

<details>
<summary><b>How does the matching algorithm work?</b></summary>
Our matching algorithm uses a combination of text similarity analysis, geospatial proximity, and temporal alignment. For text similarity, we use TF-IDF vectorization and cosine similarity, enhanced with OpenAI's language models for semantic understanding. Location matching uses the Haversine formula for geographic distance calculation.
</details>

<details>
<summary><b>Is the system GDPR compliant?</b></summary>
Yes, the Lost & Found System is designed with privacy in mind and is GDPR compliant. Users can request their data, export it, and delete their account at any time. All personal data is encrypted, and communication between users is anonymized when appropriate.
</details>

<details>
<summary><b>Can I integrate this with my existing systems?</b></summary>
Absolutely! The system provides a comprehensive RESTful API that allows integration with other platforms. We also support webhooks for real-time event notifications.
</details>

<details>
<summary><b>How can I customize the reward system?</b></summary>
The reward system is fully configurable through the administrative interface. You can adjust point values, create custom achievement badges, and configure monetary reward options.
</details>

## 👥 Contributing

We welcome contributions! Please see our [Contributing Guide](./CONTRIBUTING.md) for details on how to submit pull requests, report issues, and suggest improvements.

## 🔒 Security

If you discover any security vulnerabilities, please report them via email to [abrahamopuba@gmail.com](mailto:abrahamopuba@gmail.com). All security vulnerabilities will be promptly addressed.

## 📄 License

The Lost & Found System is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- UI components powered by [Tailwind CSS](https://tailwindcss.com) and [Alpine.js](https://alpinejs.dev)
- Icons by [Heroicons](https://heroicons.com)
- All contributors and early adopters who provided valuable feedback
