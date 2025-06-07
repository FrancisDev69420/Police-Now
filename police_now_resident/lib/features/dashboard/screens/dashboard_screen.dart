import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../auth/services/auth_service.dart';

class DashboardScreen extends StatelessWidget {
  const DashboardScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final authService = Provider.of<AuthService>(context);
    final user = authService.user;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () async {
              await authService.logout();
              if (context.mounted) {
                Navigator.pushReplacementNamed(context, '/login');
              }
            },
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Welcome, ${user?['full_name'] ?? 'User'}!',
              style: Theme.of(context).textTheme.headlineSmall,
            ),
            const SizedBox(height: 20),
            // Add your dashboard widgets here
            const Card(
              child: ListTile(
                leading: Icon(Icons.emergency),
                title: Text('Emergency Services'),
                subtitle: Text('Quick access to emergency services'),
              ),
            ),
            const SizedBox(height: 10),
            const Card(
              child: ListTile(
                leading: Icon(Icons.location_on),
                title: Text('Saved Locations'),
                subtitle: Text('Manage your saved locations'),
              ),
            ),
            const SizedBox(height: 10),
            const Card(
              child: ListTile(
                leading: Icon(Icons.history),
                title: Text('Recent Activity'),
                subtitle: Text('View your recent interactions'),
              ),
            ),
          ],
        ),
      ),
    );
  }
} 