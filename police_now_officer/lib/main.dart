import 'package:flutter/material.dart';
import 'package:police_now_officer/pages/landing_screen.dart';
import 'package:police_now_officer/pages/login_screen.dart';
import 'package:police_now_officer/pages/home_screen.dart';

void main() {
  runApp(
    MaterialApp(
      debugShowCheckedModeBanner: false,
      initialRoute: '/',
      routes: {
        '/': (context) => const LandingScreen(),
        '/login': (context) => const LoginScreen(),
        '/home': (context) => const HomeScreen(),
      },
    ),
  );
}