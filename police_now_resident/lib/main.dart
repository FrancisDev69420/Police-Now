import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'features/auth/services/auth_service.dart';
import 'features/auth/screens/login_screen.dart';
import 'features/auth/screens/registration_screen.dart';
import 'features/dashboard/screens/dashboard_screen.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthService()),
      ],
      child: ScreenUtilInit(
        designSize: const Size(375, 812), // iPhone X design size
        minTextAdapt: true,
        splitScreenMode: true,
        builder: (context, child) {
          return MaterialApp(
            title: 'Police Now',
            debugShowCheckedModeBanner: false,
            theme: ThemeData(
              primarySwatch: Colors.blue,
              useMaterial3: true,
              inputDecorationTheme: InputDecorationTheme(
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8.r),
                ),
                contentPadding: EdgeInsets.symmetric(
                  horizontal: 16.w,
                  vertical: 12.h,
                ),
              ),
              elevatedButtonTheme: ElevatedButtonThemeData(
                style: ElevatedButton.styleFrom(
                  minimumSize: Size(double.infinity, 48.h),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8.r),
                  ),
                ),
              ),
            ),
            initialRoute: '/login',
            routes: {
              '/login': (context) => const LoginScreen(),
              '/register': (context) => const RegistrationScreen(),
              '/dashboard': (context) => const DashboardScreen(),
            },
            onGenerateRoute: (settings) {
              if (settings.name == '/dashboard') {
                final authService = Provider.of<AuthService>(context, listen: false);
                if (!authService.isAuthenticated) {
                  return MaterialPageRoute(
                    builder: (context) => const LoginScreen(),
                  );
                }
              }
              return null;
            },
          );
        },
      ),
    );
  }
}
