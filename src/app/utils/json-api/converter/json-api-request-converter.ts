import JsonApiBody, { JsonApiResource } from "../json-api-body";
import JsonApiModel from "../json-api-model";

export class JsonApiRequestConverter {


  public static convertRequest(model: JsonApiModel): JsonApiBody {
    if (!model) throw new Error("model is invalid");

    const jsonApiSchema = model['jsonApi']?.schema;

    const data: JsonApiResource = {
      type: jsonApiSchema.type,
      id: model.id,
      attributes: {},
      relationships: {}
    };

    // Attributes
    for (const [attribute, property] of Object.entries<any>(jsonApiSchema.attributes)) {
      const value = model[property];
      const initValue = model.initial?.[property];

      if (JSON.stringify(value) !== JSON.stringify(initValue)) {
        data.attributes[attribute] = value;
      }
    }

    // Relationships
    for (const [relationship, property] of Object.entries<any>(jsonApiSchema.relationships)) {
      const value = model[property];
      const initValue = model.initial?.[property];

      if (Array.isArray(value)) {
        const relationshipData = value
          .map(model => model.identifier())
          .filter(identifier => identifier.id)
          .filter(identifier => !initValue
            ?.map(initModel => initModel.identifier())
            ?.some(initIdentifier => (
              identifier.type === initIdentifier.type && identifier.id === initIdentifier.id
            )));

        if (relationshipData.length != 0) {
          data.relationships[relationship] = {
            data: relationshipData
          };
        }
      } else if (value) {
        const relationshipIdentifier = value.identifier();
        const relationshipInitIdentifier = initValue?.identifier();

        if (relationshipIdentifier.id) {
          if (JSON.stringify(relationshipIdentifier) !== JSON.stringify(relationshipInitIdentifier)) {
            data.relationships[relationship] = {
              data: relationshipIdentifier
            };
          }
        }
      }
    }

    return {
      data: data
    }
  }

  convert(model: any): string {
    if (!model) throw new Error("model is invalid");

    let data: JsonApiResource = {
      type: model.jsonApi?.type,
      id: model[model.jsonApi?.mapping?.id || 'id'],
      attributes: {},
      relationships: {}
    };

    // Attributes
    for (const [attribute, property] of Object.entries<any>(model.jsonApi?.mapping?.attributes)) {
      const value = model[property];
      const initValue = model.initial?.[property];

      if (JSON.stringify(value) !== JSON.stringify(initValue)) {
        data.attributes[attribute] = value;
      }
    }

    // Relationships
    for (const [relationship, property] of Object.entries<any>(model.jsonApi?.mapping?.relationships)) {
      const value = model[property];
      const initValue = model.initial?.[property];

      if (Array.isArray(value)) {
        const relationshipData = value
          .map(model => model.identifier())
          .filter(identifier => identifier.id)
          .filter(identifier => !initValue
            ?.map(initModel => initModel.identifier())
            ?.some(initIdentifier => (
              identifier.type === initIdentifier.type && identifier.id === initIdentifier.id
            )));

        if (relationshipData.length != 0) {
          data.relationships[relationship] = {
            data: relationshipData
          };
        }
      } else if (value) {
        const relationshipIdentifier = value.identifier();
        const relationshipInitIdentifier = initValue?.identifier();

        if (relationshipIdentifier.id) {
          if (JSON.stringify(relationshipIdentifier) !== JSON.stringify(relationshipInitIdentifier)) {
            data.relationships[relationship] = {
              data: relationshipIdentifier
            };
          }
        }
      }
    }

    // if (modelValue.id && modelValue.raw) {
    //   for (let attribute in modelValue.raw.attributes) {
    //     // TODO: To camelCase
    //     if (JSON.stringify(modelValue[attribute]) !== JSON.stringify(modelValue.raw.attributes[attribute])) {
    //       data.attributes[attribute] = modelValue[attribute];
    //     }
    //   }

    //   for (let relationship in modelValue.raw.relationships) {
    //     // TODO: to camelCase
    //     if (modelValue[relationship]) {
    //       if (Array.isArray(modelValue[relationship])) {
    //         const relationshipData = modelValue[relationship]
    //           .map(model => {
    //             return {
    //               type: model.constructor.type,
    //               id: model.id
    //             }
    //           })
    //           .filter(d => d.id)
    //           .filter(data => {
    //             return modelValue.raw.relationships[relationship].data
    //               .findIndex(d => d.type === data.type && d.id === data.id) === -1;
    //           });

    //         if (relationshipData.length != 0) {
    //           data.relationships[relationship] = {
    //             data: relationshipData
    //           };
    //         }
    //       } else {
    //         if (modelValue[relationship].constructor.type !== modelValue.raw.relationships[relationship].data.type ||
    //           modelValue[relationship].id !== modelValue.raw.relationships[relationship].data.id) {
    //           if (modelValue[relationship].id) {
    //             data.relationships[relationship] = {
    //               data: {
    //                 type: modelValue[relationship].constructor.type,
    //                 id: modelValue[relationship].id
    //               }
    //             };
    //           }
    //         }
    //       }
    //     }
    //   }
    // } else {
    //   const attributes: any[] = [];
    //   const relationships: any[] = [];

    //   for (let name in modelValue) {
    //     if (this.isAttribute(modelValue[name])) {
    //       attributes.push(name);
    //     } else if (this.isRelationship(modelValue[name])) {
    //       relationships.push(name);
    //     }
    //   }


    //   for (let attribute of attributes) {
    //     // TODO: To camelCase
    //     data.attributes[attribute] = modelValue[attribute];
    //   }

    //   for (let relationship of relationships) {
    //     // TODO: to camelCase
    //     if (modelValue[relationship]) {
    //       if (Array.isArray(modelValue[relationship])) {
    //         const relationshipData = modelValue[relationship]
    //           .map(model => {
    //             return {
    //               type: model.constructor.type,
    //               id: model.id
    //             }
    //           })
    //           .filter(d => d.id);

    //         if (relationshipData.length != 0) {
    //           data.relationships[relationship] = {
    //             data: relationshipData
    //           };
    //         }
    //       } else {
    //         if (modelValue[relationship].id) {
    //           data.relationships[relationship] = {
    //             data: {
    //               type: modelValue[relationship].constructor.type,
    //               id: modelValue[relationship].id
    //             }
    //           };
    //         }
    //       }
    //     }
    //   }
    // }

    return JSON.stringify({
      data: data
    });
  }

  private isAttribute(value: any): boolean {
    if (Array.isArray(value)) {
      if (value.length > 0 && value.every(model => {
        return typeof model === "string" ||
          typeof model === "number" ||
          typeof model === "boolean" ||
          model.constructor.name === "Object";
      })) {
        return true;
      }
    } else if (value) {
      if (typeof value === "string" ||
        typeof value === "number" ||
        typeof value === "boolean" ||
        value.constructor.name === "Object") {
        return true;
      }
    }

    return false;
  }
  private isRelationship(value: any): boolean {
    if (Array.isArray(value)) {
      if (value.length > 0 && value.every(model => {
        return typeof model !== "string" &&
          typeof model !== "number" &&
          typeof model !== "boolean" &&
          model.constructor.name !== "Object";
      })) {
        return true;
      }
    } else if (value) {
      if (typeof value !== "string" &&
        typeof value !== "number" &&
        typeof value !== "boolean" &&
        value.constructor.name !== "Object") {
        return true;
      }
    }

    return false;
  }
}
