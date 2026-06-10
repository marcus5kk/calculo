import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';
import '../models/usuario.dart';

class ApiService {
  static const String _tokenKey = 'auth_token';
  static const String _userKey = 'user_data';

  static Future<Map<String, dynamic>> login(
      String email, String senha) async {
    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/login.php'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'email': email, 'senha': senha}),
      );

      final data = jsonDecode(response.body);
      if (response.statusCode == 200 && data['sucesso'] == true) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString(_tokenKey, data['token'] ?? '');
        await prefs.setString(_userKey, jsonEncode(data['usuario']));
      }
      return data;
    } catch (e) {
      return {'sucesso': false, 'mensagem': 'Erro de conexão. Verifique sua internet.'};
    }
  }

  static Future<Map<String, dynamic>> register(
      String nome, String email, String senha) async {
    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/register.php'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'nome': nome, 'email': email, 'senha': senha}),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'sucesso': false, 'mensagem': 'Erro de conexão. Verifique sua internet.'};
    }
  }

  static Future<Map<String, dynamic>> criarFuncionario(
      String nome, String email, String senha, int empresaId) async {
    try {
      final token = await getToken();
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/create_funcionario.php'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode({
          'nome': nome,
          'email': email,
          'senha': senha,
          'empresa_id': empresaId,
        }),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'sucesso': false, 'mensagem': 'Erro de conexão. Verifique sua internet.'};
    }
  }

  static Future<List<dynamic>> getFuncionarios(int empresaId) async {
    try {
      final token = await getToken();
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/get_funcionarios.php?empresa_id=$empresaId'),
        headers: {'Authorization': 'Bearer $token'},
      );
      final data = jsonDecode(response.body);
      if (data['sucesso'] == true) {
        return data['funcionarios'] ?? [];
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_tokenKey);
  }

  static Future<Usuario?> getUsuarioSalvo() async {
    final prefs = await SharedPreferences.getInstance();
    final userJson = prefs.getString(_userKey);
    if (userJson == null) return null;
    try {
      return Usuario.fromJson(jsonDecode(userJson));
    } catch (e) {
      return null;
    }
  }

  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
    await prefs.remove(_userKey);
  }
}
