import 'package:flutter/material.dart';
import '../../models/usuario.dart';
import '../../services/api_service.dart';
import '../login_screen.dart';
import 'funcionarios_screen.dart';

class EstabelecimentoHomeScreen extends StatelessWidget {
  final Usuario usuario;

  const EstabelecimentoHomeScreen({super.key, required this.usuario});

  Future<void> _logout(BuildContext context) async {
    await ApiService.logout();
    if (!context.mounted) return;
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(builder: (_) => const LoginScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: const Text('AgendaJá — Empresa'),
        backgroundColor: const Color(0xFF2E7D32),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            tooltip: 'Sair',
            onPressed: () => _logout(context),
          ),
        ],
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFF2E7D32), Color(0xFF66BB6A)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.25),
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: const Text(
                        'ESTABELECIMENTO',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          letterSpacing: 1,
                        ),
                      ),
                    ),
                    const SizedBox(height: 10),
                    Text(
                      usuario.nome,
                      style: const TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      usuario.email,
                      style: const TextStyle(color: Colors.white70, fontSize: 13),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 28),
              const Text(
                'Painel do Estabelecimento',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF2E7D32),
                ),
              ),
              const SizedBox(height: 16),
              Expanded(
                child: GridView.count(
                  crossAxisCount: 2,
                  crossAxisSpacing: 12,
                  mainAxisSpacing: 12,
                  children: [
                    _MenuCard(
                      icon: Icons.group,
                      label: 'Funcionários',
                      sublabel: 'Gerenciar equipe',
                      color: const Color(0xFF2E7D32),
                      habilitado: true,
                      onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) =>
                              FuncionariosScreen(empresa: usuario),
                        ),
                      ),
                    ),
                    _MenuCard(
                      icon: Icons.calendar_month,
                      label: 'Agenda',
                      sublabel: 'Em breve',
                      color: const Color(0xFF0288D1),
                      habilitado: false,
                      onTap: null,
                    ),
                    _MenuCard(
                      icon: Icons.design_services,
                      label: 'Serviços',
                      sublabel: 'Em breve',
                      color: const Color(0xFF6A1B9A),
                      habilitado: false,
                      onTap: null,
                    ),
                    _MenuCard(
                      icon: Icons.bar_chart,
                      label: 'Relatórios',
                      sublabel: 'Em breve',
                      color: const Color(0xFFE65100),
                      habilitado: false,
                      onTap: null,
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _MenuCard extends StatelessWidget {
  final IconData icon;
  final String label;
  final String sublabel;
  final Color color;
  final bool habilitado;
  final VoidCallback? onTap;

  const _MenuCard({
    required this.icon,
    required this.label,
    required this.sublabel,
    required this.color,
    required this.habilitado,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: habilitado
          ? onTap
          : () {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Em breve disponível!')),
              );
            },
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.06),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 28),
            ),
            const SizedBox(height: 12),
            Text(
              label,
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 13,
              ),
            ),
            const SizedBox(height: 2),
            Text(
              sublabel,
              style: TextStyle(fontSize: 11, color: Colors.grey[500]),
            ),
          ],
        ),
      ),
    );
  }
}
