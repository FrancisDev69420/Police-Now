import 'package:flutter/material.dart';
import 'package:police_now_officer/pages/login_screen.dart';

class LandingScreen extends StatefulWidget {
  const LandingScreen({super.key});

  @override
  State<LandingScreen> createState() => _LandingScreenState();
}

class _LandingScreenState extends State<LandingScreen> {
  @override
  void initState() {
    super.initState();
    // Simulate loading time - you can replace this with actual initialization logic
    _loadApp();
  }  Future<void> _loadApp() async {
    // Simulate app initialization (replace with your actual loading logic)
    print('Loading started...');
    await Future.delayed(const Duration(seconds: 3));
    print('Loading completed, navigating to login...');
    
    // Navigate to login screen after loading
    if (mounted) {
      print('Widget is mounted, navigating...');
      Navigator.pushReplacement(
        context, 
        MaterialPageRoute(builder: (context) => const LoginScreen())
      );
    } else {
      print('Widget not mounted, cannot navigate');
    }
  }
  @override
  Widget build(BuildContext context) {
    print('Landing screen build method called');
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          color: Color.fromARGB(255, 44, 10, 195),
        ),        
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // Logo
              Image.asset(
                'assets/images/police-now-logo.jpg',
                width: 200,
                height: 200,
              ),              const SizedBox(height: 50),
              
              // Loading progress bar
              Container(
                width: 250,
                height: 4,
                child: const LinearProgressIndicator(
                  valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                  backgroundColor: Colors.white30,
                ),
              ),
              const SizedBox(height: 20),
              
              // Loading text
              const Text(
                'Loading...',
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 16,
                  fontWeight: FontWeight.w300,                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}