class Usuario {
  final int id;
  final String nome;
  final String email;
  final String tipoUsuario;
  final int? empresaId;
  final String? token;

  Usuario({
    required this.id,
    required this.nome,
    required this.email,
    required this.tipoUsuario,
    this.empresaId,
    this.token,
  });

  factory Usuario.fromJson(Map<String, dynamic> json) {
    return Usuario(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      nome: json['nome'] ?? '',
      email: json['email'] ?? '',
      tipoUsuario: json['tipo_usuario'] ?? 'usuario_final',
      empresaId: json['empresa_id'] != null
          ? (json['empresa_id'] is int
              ? json['empresa_id']
              : int.tryParse(json['empresa_id'].toString()))
          : null,
      token: json['token'],
    );
  }

  bool get isUsuarioFinal => tipoUsuario == 'usuario_final';
  bool get isEstabelecimento => tipoUsuario == 'estabelecimento';
  bool get isFuncionario => tipoUsuario == 'funcionario';
}
