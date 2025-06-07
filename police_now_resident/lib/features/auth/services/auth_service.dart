import 'package:flutter/foundation.dart';
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class AuthResult {
  final bool success;
  final String message;
  final String? token;
  final Map<String, dynamic>? user;

  AuthResult({
    required this.success,
    required this.message,
    this.token,
    this.user,
  });
}

class AuthService extends ChangeNotifier {
  // Update with your actual backend URL
  static const String baseUrl = 'http://10.0.2.2:8000/api'; // For Android Emulator
  // static const String baseUrl = 'http://localhost:8000/api'; // For iOS Simulator
  // static const String baseUrl = 'http://your-actual-ip:8000/api'; // For physical device

  final Dio _dio = Dio();
  final FlutterSecureStorage _secureStorage = const FlutterSecureStorage();
  
  String? _token;
  bool _isAuthenticated = false;
  Map<String, dynamic>? _user;

  bool get isAuthenticated => _isAuthenticated;
  String? get token => _token;
  Map<String, dynamic>? get user => _user;

  AuthService() {
    _initializeDio();
    _loadStoredToken();
  }

  void _initializeDio() {
    _dio.options.baseUrl = baseUrl;
    _dio.options.connectTimeout = const Duration(seconds: 30);
    _dio.options.receiveTimeout = const Duration(seconds: 30);
    _dio.options.sendTimeout = const Duration(seconds: 30);
    _dio.options.headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    
    // Add interceptor for logging
    if (kDebugMode) {
      _dio.interceptors.add(LogInterceptor(
        requestBody: true,
        responseBody: true,
        logPrint: (object) => debugPrint(object.toString()),
      ));
    }

    // Add error handling interceptor
    _dio.interceptors.add(InterceptorsWrapper(
      onError: (DioException e, ErrorInterceptorHandler handler) {
        if (e.type == DioExceptionType.connectionTimeout ||
            e.type == DioExceptionType.sendTimeout ||
            e.type == DioExceptionType.receiveTimeout) {
          debugPrint('Connection timeout occurred. Please check your internet connection and server status.');
          return handler.reject(DioException(
            requestOptions: e.requestOptions,
            error: 'Connection timeout. Please check your internet connection and try again.',
            type: DioExceptionType.connectionTimeout,
          ));
        }
        return handler.next(e);
      },
    ));
  }

  Future<void> _loadStoredToken() async {
    try {
      final storedToken = await _secureStorage.read(key: 'auth_token');
      if (storedToken != null) {
        _token = storedToken;
        _dio.options.headers['Authorization'] = 'Bearer $_token';
        await getUser(); // Verify token and get user data
      }
    } catch (e) {
      debugPrint('Error loading stored token: $e');
    }
  }

  Future<void> _saveToken(String token) async {
    await _secureStorage.write(key: 'auth_token', value: token);
    _token = token;
    _dio.options.headers['Authorization'] = 'Bearer $_token';
  }

  Future<void> _clearToken() async {
    await _secureStorage.delete(key: 'auth_token');
    _token = null;
    _dio.options.headers.remove('Authorization');
  }

  Future<AuthResult> login(String username, String password) async {
    try {
      final response = await _dio.post(
        '/login',
        data: {
          'username': username,
          'password': password,
        },
      );

      if (response.statusCode == 200) {
        final data = response.data;
        await _saveToken(data['token']);
        _user = data['user'];
        _isAuthenticated = true;
        notifyListeners();
        
        return AuthResult(
          success: true,
          message: 'Login successful',
          token: _token,
          user: _user,
        );
      } else {
        return AuthResult(
          success: false,
          message: response.data['message'] ?? 'Login failed',
        );
      }
    } on DioException catch (e) {
      String errorMessage = 'An error occurred';
      
      if (e.type == DioExceptionType.connectionTimeout ||
          e.type == DioExceptionType.sendTimeout ||
          e.type == DioExceptionType.receiveTimeout) {
        errorMessage = 'Connection timeout. Please check your internet connection and server status.';
      } else if (e.type == DioExceptionType.connectionError) {
        errorMessage = 'Unable to connect to the server. Please check your internet connection.';
      } else if (e.response?.statusCode == 401) {
        errorMessage = 'Invalid username or password';
      } else if (e.response?.data != null && e.response?.data['message'] != null) {
        errorMessage = e.response?.data['message'];
      }
      
      debugPrint('Login error: ${e.message}');
      return AuthResult(success: false, message: errorMessage);
    } catch (e) {
      debugPrint('Unexpected error during login: $e');
      return AuthResult(
        success: false,
        message: 'An unexpected error occurred',
      );
    }
  }

  Future<AuthResult> register({
    required String username,
    required String email,
    required String password,
    required String fullName,
    String? phoneNumber,
    String? address,
    String? emergencyContactName,
    String? emergencyContactNumber,
  }) async {
    try {
      final response = await _dio.post(
        '/register',
        data: {
          'username': username,
          'email': email,
          'password': password,
          'full_name': fullName,
          'phone_number': phoneNumber,
          'address': address,
          'emergency_contact_name': emergencyContactName,
          'emergency_contact_number': emergencyContactNumber,
        },
      );

      if (response.statusCode == 201) {
        final data = response.data;
        await _saveToken(data['token']);
        _user = data['user'];
        _isAuthenticated = true;
        notifyListeners();
        
        return AuthResult(
          success: true,
          message: 'Registration successful',
          token: _token,
          user: _user,
        );
      } else {
        return AuthResult(
          success: false,
          message: response.data['message'] ?? 'Registration failed',
        );
      }
    } on DioException catch (e) {
      String message = 'Registration failed';
      if (e.response?.data != null && e.response?.data['message'] != null) {
        message = e.response?.data['message'];
      } else if (e.type == DioExceptionType.connectionTimeout) {
        message = 'Connection timeout. Please check your internet connection.';
      } else if (e.type == DioExceptionType.connectionError) {
        message = 'Could not connect to the server. Please check your internet connection.';
      }
      return AuthResult(success: false, message: message);
    } catch (e) {
      return AuthResult(
        success: false,
        message: 'Registration failed: ${e.toString()}',
      );
    }
  }

  Future<void> logout() async {
    if (_token != null) {
      try {
        await _dio.post('/logout');
      } catch (e) {
        debugPrint('Logout error: ${e.toString()}');
      }
    }
    
    await _clearToken();
    _user = null;
    _isAuthenticated = false;
    notifyListeners();
  }

  Future<AuthResult> resetPassword(String email) async {
    try {
      final response = await _dio.post(
        '/reset-password',
        data: {'email': email},
      );

      if (response.statusCode == 200) {
        return AuthResult(
          success: true,
          message: response.data['message'] ?? 'Password reset instructions sent to your email',
        );
      } else {
        return AuthResult(
          success: false,
          message: response.data['message'] ?? 'Password reset failed',
        );
      }
    } on DioException catch (e) {
      String message = 'Password reset failed';
      if (e.response?.data != null && e.response?.data['message'] != null) {
        message = e.response?.data['message'];
      } else if (e.type == DioExceptionType.connectionTimeout) {
        message = 'Connection timeout. Please check your internet connection.';
      } else if (e.type == DioExceptionType.connectionError) {
        message = 'Could not connect to the server. Please check your internet connection.';
      }
      return AuthResult(success: false, message: message);
    } catch (e) {
      return AuthResult(
        success: false,
        message: 'Password reset failed: ${e.toString()}',
      );
    }
  }

  Future<AuthResult> getUser() async {
    if (_token == null) {
      return AuthResult(
        success: false,
        message: 'Not authenticated',
      );
    }

    try {
      final response = await _dio.get('/user');

      if (response.statusCode == 200) {
        _user = response.data;
        _isAuthenticated = true;
        notifyListeners();
        
        return AuthResult(
          success: true,
          message: 'User data retrieved successfully',
          user: _user,
        );
      } else {
        return AuthResult(
          success: false,
          message: response.data['message'] ?? 'Failed to get user data',
        );
      }
    } on DioException catch (e) {
      if (e.response?.statusCode == 401) {
        await _clearToken();
        _isAuthenticated = false;
        notifyListeners();
      }
      String message = 'Failed to get user data';
      if (e.response?.data != null && e.response?.data['message'] != null) {
        message = e.response?.data['message'];
      } else if (e.type == DioExceptionType.connectionTimeout) {
        message = 'Connection timeout. Please check your internet connection.';
      } else if (e.type == DioExceptionType.connectionError) {
        message = 'Could not connect to the server. Please check your internet connection.';
      }
      return AuthResult(success: false, message: message);
    } catch (e) {
      return AuthResult(
        success: false,
        message: 'Failed to get user data: ${e.toString()}',
      );
    }
  }
} 