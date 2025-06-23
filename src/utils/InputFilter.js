// src/utils/inputFilter.js
/* eslint-disable */
const palavrasSexuais = [
  "buceta", "bct", "xereca", "xoxota", "xana", "checa", "chana", "periquita",
  "clitoris", "clitóris", "xaninha", "vagina", "piriquita", "pinto", "pau", "caralho",
  "rola", "rolao", "falo", "piroca", "cacete", "penis", "pênis", "c@ralho", "c*ralho",
  "foda", "foder", "foda-se", "fudido", "fudida", "fudendo", "trepada", "trepar",
  "comer alguém", "gozar", "gozando", "sexo", "transar", "chupar", "boquete",
  "broche", "lamber", "sexo oral", "sexo anal", "meter", "enfiar", "penetração",
  "punheta", "punheteiro", "gozada", "gozo", "tesão", "tarado", "tarada",
  "siririca", "masturbação", "masturbar", "ejacular", "ejaculação", "safado",
  "safada", "sacanagem", "putaria", "puto", "puta", "putinha", "vagabunda",
  "vagabundo", "delícia", "delicia", "prazer", "peitinho", "peito", "raba",
  "bundão", "bundinha", "nudes", "nude", "pelado", "pelada", "gemendo",
  "gemido", "gemendo gostoso", "gemer", "gemidos", "tesuda", "tesudo"
]

const palavrasProibidas = [
  "puta", "puto", "putinha", "bosta", "merda", "cocô", "porra", "caralho","http","https",
  "krl", "kct", "cacet*", "cacete", "foda", "fod*", "fdp", "f*d*", "f*da",
  "foda-se", "fodase", "vai se fuder", "vai se ferrar", "vai tomar no cu",
  "vtc", "vai tnc", "vai pro inferno", "diabo", "capeta", "pnc", "pau no cu",
  "xereca", "xoxota", "periquita", "xana", "xaninha", "checa", "chana",
  "prostituta", "piranha", "vadia", "vagaba", "rapariga", "cadel*", "cachorra",
  "arrombado", "corno", "corna", "trouxa", "otário", "otaria", "babaca", "idiota",
  "imbecil", "retardado", "retardada", "burro", "burra", "lesado", "nojent*",
  "nojenta", "nojento", "escrot*", "escroto", "escrota", "palhaço", "palhaça",
  "bicha", "bich*", "viado", "boiola", "gayzinho", "sapatão", "traveco", "travec*",
  "sem vergonha", "sem-vergonha", "macaco", "macaca", "preto fedido",
  "branquelo", "judeu fdp", "crente lixo", "crentelho", "mulçumano terrorista",
  "terrorista", "lixo", "verme", "escória"
]

// Regex para links (http:// ou www.)
const regexLink = /(https?:\/\/|www\.)\S+/i

// Regex para palavras ofensivas
const regexPalavras = new RegExp(
  [...palavrasSexuais, ...palavrasProibidas]
    .map(p => p.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")) // escapa caracteres especiais
    .join("|"),
  "i" // case-insensitive
)

/**
 * Verifica se o texto contém links ou palavras impróprias
 * @param {string} texto
 * @returns {boolean} true se houver conteúdo proibido
 */
export function contemConteudoProibido(texto) {
  if (!texto) return false

  const textoLimpo = texto
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "") // remove acentos
    .toLowerCase()

  return regexLink.test(textoLimpo) || regexPalavras.test(textoLimpo)
}
