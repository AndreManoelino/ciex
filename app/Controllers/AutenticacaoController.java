package com.seuusuario.sistemaregional.controller;

import com.seuusuario.sistemaregional.model.Usuario;
import com.seuusuario.sistemaregional.repository.UsuarioRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.*;

@RestController
@RequestMapping("/api/auth")
@CrossOrigin(origins = "*")
public class AutenticacaoController {

    @Autowired
    private UsuarioRepository usuarioRepository;

    // Login
    @PostMapping("/login")
    public ResponseEntity<?> login(@RequestBody Map<String, String> dados) {
        String cpf = dados.get("cpf");
        String senha = dados.get("senha");

        Optional<Usuario> user = usuarioRepository.findByCpf(cpf);
        if (user.isPresent() && user.get().getSenha().equals(senha)) {
            return ResponseEntity.ok(user.get());
        } else {
            return ResponseEntity.status(401).body("CPF ou senha inválidos");
        }
    }

    // Registro
    @PostMapping("/registrar")
    public ResponseEntity<?> registrar(@RequestBody Map<String, String> dados) {
        Usuario usuario = new Usuario();

        usuario.setNome(dados.get("nome"));
        usuario.setCpf(dados.get("cpf"));
        usuario.setEmail(dados.get("email"));
        usuario.setSenha(dados.get("senha"));
        usuario.setSenhaSmtp(dados.get("senha_smtp"));
        usuario.setTipo(dados.get("tipo"));
        usuario.setEstado(dados.get("estado"));

        if ("supervisor".equals(dados.get("tipo"))) {
            usuario.setUnidade("TODAS_DO_ESTADO");
        } else {
            usuario.setUnidade(dados.get("unidade"));
        }

        try {
            Usuario novo = usuarioRepository.save(usuario);
            return ResponseEntity.ok(novo);
        } catch (Exception e) {
            return ResponseEntity.status(500).body("Erro ao registrar: " + e.getMessage());
        }
    }

    // Recuperar senha
    @PostMapping("/recuperar-senha")
    public ResponseEntity<?> recuperarSenha(@RequestBody Map<String, String> dados) {
        String cpf = dados.get("cpf");
        String novaSenha = dados.get("senha");

        Optional<Usuario> user = usuarioRepository.findByCpf(cpf);
        if (user.isPresent()) {
            Usuario u = user.get();
            u.setSenha(novaSenha);
            usuarioRepository.save(u);
            return ResponseEntity.ok("Senha atualizada com sucesso");
        } else {
            return ResponseEntity.status(404).body("CPF não encontrado");
        }
    }

    // Estados e Unidades
    @GetMapping("/estados")
    public List<String> getEstados() {
        return Arrays.asList("Minas Gerais", "São Paulo", "Rio de Janeiro");
    }

    @GetMapping("/unidades")
    public Map<String, List<String>> getUnidades() {
        Map<String, List<String>> unidades = new HashMap<>();
        unidades.put("Minas Gerais", Arrays.asList(
                "Barreiro", "Betim", "Contagem", "Contagem Avançada", "Curvelo",
                "Governador Valadares", "Ipatinga", "Juiz de Fora", "Montes Claros",
                "Poços de Caldas", "Pouso Alegre", "Praça Sete", "São Sebastião do Paraiso",
                "Sete Lagoas", "Sete Lagoas Avançada", "Teofilo Otoni", "Uberlândia",
                "Uberlândia Avançada", "Varginha"
        ));
        unidades.put("São Paulo", Arrays.asList("Unidade SP1", "Unidade SP2", "Unidade SP3"));
        unidades.put("Rio de Janeiro", Arrays.asList("Unidade RJ1", "Unidade RJ2"));
        return unidades;
    }
}
